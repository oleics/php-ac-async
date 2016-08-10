<?php

namespace Ac\Async;

use \Exception;

use Ac\Async\Promise\FakeNullTrait;
use Ac\Async\Promise\ThenableTrait;
use Ac\Async\Promise\Task;

define('AC_ASYNC_PROMISE_STATE_PENDING',   1);
define('AC_ASYNC_PROMISE_STATE_FULFILLED', 2);
define('AC_ASYNC_PROMISE_STATE_REJECTED',  3);

/**
 * THE promise-implementation for PHP: Lightweight, dependency-free, rock-solid.
 *
 * Supports NULL as return-values via Promise::nullAsResult().
 */
final class Promise {

  use FakeNullTrait;
  use ThenableTrait;

  const STATE_PENDING   = AC_ASYNC_PROMISE_STATE_PENDING;
  const STATE_FULFILLED = AC_ASYNC_PROMISE_STATE_FULFILLED;
  const STATE_REJECTED  = AC_ASYNC_PROMISE_STATE_REJECTED;

  protected $state = AC_ASYNC_PROMISE_STATE_PENDING;

  protected $result;
  protected $reason;

  protected $chain = [];

  /**
   * @param callable $executor
   * @return void
   */
  public function __construct(callable $executor) {
    try {
      $resultReturned;
      $resultReturned = call_user_func(
        $executor,
        // resolve
        function() use(&$resultReturned) {
          if($this->state !== self::STATE_PENDING) {
            throw new Exception('Promise is already fulfilled or rejected.');
          }
          if(func_num_args() === 0) {
            $result = $resultReturned;
          } else {
            $result = func_get_arg(0);
          }
          $this->result = $result;
          $this->state = self::STATE_FULFILLED;
          $this->exec_chain();
        },
        // reject
        function($reason) {
          if($this->state !== self::STATE_PENDING) {
            throw new Exception('Promise already fulfilled or rejected.');
          }
          $this->reason = $reason;
          $this->state = self::STATE_REJECTED;
          $this->exec_chain();
        }
      );
    } catch(Exception $reason) {
      $this->reason = $reason;
      $this->state = self::STATE_REJECTED;
      $this->exec_chain();
    }
  }

  /**
   * Implements `thenable`
   * @param callable|null $onFulfilled
   * @param callable|null $onRejected
   * @return $this
   */
  public function then(callable $onFulfilled = null, callable $onRejected = null) {
    $this->chain[] = [$onFulfilled, null];
    if(isset($onRejected)) {
      $this->chain[] = [null, $onRejected];
    }
    return $this->exec_chain();
  }

  /**
   * @param callable $fn
   * @return $this
   */
  public function catchReject(callable $onRejected) {
    $this->chain[] = [null, $onRejected];
    return $this->exec_chain();
  }

  /**
   * @return Promise
   */
  protected function exec_chain() {
    if($this->state === self::STATE_PENDING || empty($this->chain)) {
      return $this;
    }

    $chain = array_reverse($this->chain);
    $this->chain = [];
    $previousResult = $this->result;
    $result = $this->result;
    while(($fn = array_pop($chain)) !== null) {
      // state: fulfilled
      if($this->state === self::STATE_FULFILLED && isset($fn[0])) {
        $fn = $fn[0];

        $previousResult = $result;
        try {
          $result = call_user_func($fn, self::isFakeNull($result) ? null : $result);
        } catch(Exception $reason) {
          $this->reason = $reason;
          $this->state = self::STATE_REJECTED;
          continue;
        }
        if($result === null) {
          $result = $previousResult;
        }

        if($result instanceof Promise) {
          $result->chain = array_merge($result->chain, array_reverse($chain), $this->chain);
          $chain = [];
          $this->chain = [];
          if($result->state !== self::STATE_PENDING) {
            $result->exec_chain();
          }
          break;
        }
      } else
      // state: rejected
      if($this->state === self::STATE_REJECTED && isset($fn[1])) {
        $this->state = self::STATE_FULFILLED;
        $fn = $fn[1];
        $previousResult = $result;
        $result = call_user_func($fn, $this->reason);
        if($result === null) {
          $result = $previousResult;
        }
      }
    }

    if($result === null) {
      $result = $this;
    } else if(!($result instanceof Promise)) {
      $result = Promise::resolve(self::isFakeNull($result) ? null : $result);
    }

    return $result;
  }

  // Static

  /**
   * @param mixed|Promise|Thenable $value
   * @return Promise
   */
  static public function resolve($value = null) {
    return new Promise(function($resolve, $reject) use(&$value) {
      if($value instanceof Promise) {
        if($value->state === self::STATE_PENDING) {
          $value->then($resolve, $reject);
        } else if($value->state === self::STATE_REJECTED) {
          $reject($value->reason);
        } else if($value->state === self::STATE_FULFILLED) {
          $resolve($value->result);
        }
      } else if(self::isThenable($value)) {
        self::runThenable($value, $resolve, $reject);
      } else {
        $resolve($value);
      }
    });
  }

  /**
   * @param mixed $reason
   * @return Promise
   */
  static public function reject($reason) {
    return new Promise(function($resolve, $reject) use(&$reason) {
      $reject($reason);
    });
  }

  /**
   * @param array $iterable
   * @return Promise
   */
  static public function all(array $iterable) {
    return new Promise(function($resolve, $reject) use(&$iterable) {
      $pending = count($iterable);
      $results = [];
      $someRejected = false;

      $catchReject = function($reason) use(&$reject, &$pending, &$someRejected) {
        if($someRejected) return;
        $someRejected = true;
        $reject($reason);
      };

      foreach($iterable as $key => $value) {
        Promise::resolve($value)->then(function($value) use(&$resolve, &$pending, &$results, &$someRejected, $key) {
          if($someRejected === true) return;
          $results[$key] = $value;
          if(--$pending > 0) return;
          ksort($results);
          $resolve($results);
        }, $catchReject);
      }
    });
  }

  /**
   * @param array $iterable
   * @param int $maxParallel
   * @return Promise
   */
  static public function parallel(array $iterable, $maxParallel = PHP_INT_MAX) {
    return new Promise(function($resolve, $reject) use(&$iterable, &$maxParallel) {
      $total = $pending = count($iterable);
      $running = 0;
      $key = -1;
      $results = [];
      $dequeue;
      $check;

      $dequeue = function() use(&$iterable, &$maxParallel, &$total, &$running, &$key, &$check) {
        $key++;
        if($key >= $total) return false;
        Promise::resolve($iterable[$key])->then(function($value) use(&$running, &$check, $key) {
          --$running;
          $check($key, $value);
        }, function($reason) use(&$running, &$check, $key) {
          --$running;
          $check($key, null, $reason);
        });
        if(++$running >= $maxParallel) return false;
        return true;
      };

      $check = function($key, $value, $reason = null) use(&$resolve, &$pending, &$dequeue, &$results) {
        $results[$key] = [$value, $reason];
        if(--$pending > 0) {
          $dequeue();
          return;
        }
        ksort($results);
        $resolve($results);
      };

      while($dequeue()) {}
    });
  }

  /**
   * @return FakeNull
   */
  static public function nullAsResult() {
    return self::fakeNull();
  }

}

Promise::initFakeNullTrait();
