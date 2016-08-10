<?php

namespace Ac\Async\Promise;

use \Exception;

trait ThenableTrait {
  /**
   * @param mixed $value
   * @return bool
   */
  static public function isThenable($value) {
    if(is_array($value)) {
      return isset($value['then']);
    } else if(is_callable($value)) {
      return true;
    } else if(is_object($value)) {
      return method_exists($value, 'then');
    } else {
      return false;
    }
  }

  /**
   * @param thenable $value
   * @return void
   */
  static public function runThenable($value, callable $onFulfilled = null, callable $onRejected = null) {
    if(is_array($value)) {
      $value['then']($onFulfilled, $onRejected);
    } else if(is_callable($value)) {
      call_user_func_array($value, [&$onFulfilled, &$onRejected]);
    } else if(is_object($value)) {
      $value->then($onFulfilled, $onRejected);
    } else {
      throw new Exception('Not a thenable.');
    }
  }
}
