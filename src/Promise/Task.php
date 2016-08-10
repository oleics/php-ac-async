<?php

namespace Ac\Async\Promise;

use Ac\Async\Promise;

/**
 * Provides late-execution of a promise-executor-function.
 */
class Task {

  protected $executor;
  protected $_promise;

  /**
   * @return void
   */
  public function __construct(callable $executor) {
    $this->executor = $executor;
  }

  /**
   * Returns the promise of the task. The first call will create the promise, thus start the task.
   * @return Promise
   */
  public function promise() {
    if(!isset($this->_promise)) {
      $this->_promise = new Promise($this->executor);
    }
    return $this->_promise;
  }

  /**
   * Implements `thenable`
   * @param callable $onFulfilled
   * @param callable $onRejected
   * @return void
   */
  public function then(callable $onFulfilled = null, callable $onRejected = null) {
    $this->promise()->then($onFulfilled, $onRejected);
  }

  // Static

  /**
   * Returns a factory-function for task-instances of $executor.
   * @param callable $executor
   * @return callable
   */
  static public function provider(callable $executor) {
    return function() use(&$executor) {
      return new Task($executor);
    };
  }
}
