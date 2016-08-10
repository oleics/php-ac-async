<?php

namespace Ac\Async\Promise;

class FakeNull {}

/**
 *  Allows for NULL as a return-value.
 */
trait FakeNullTrait {

  protected static $fakeNull;

  static public function initFakeNullTrait() {
    if(!isset(self::$fakeNull)) {
      self::$fakeNull = new FakeNull();
    }
  }

  static public function &fakeNull() {
    return self::$fakeNull;
  }

  static public function isFakeNull(&$value) {
    return ($value instanceof FakeNull);
  }

}
