<?php

namespace Ac\Async\Promise;

trait NonBlockingModeTrait {

  static protected $nonBlockingMode = false;

  static public function nonBlockingModeIsAvailable() {
    return class_exists('\Ac\Async\Async', true);
  }

  static public function enableNonBlockingMode() {
    if(!self::nonBlockingModeIsAvailable()) {
      throw new Exception('Can not enable non-blocking-mode: Not available.');
    }
    self::$nonBlockingMode = true;
  }

  static public function disableNonBlockingMode() {
    self::$nonBlockingMode = false;
  }

  static public function isNonBlockingMode() {
    return self::$nonBlockingMode;
  }

}
