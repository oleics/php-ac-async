<?php

namespace Ac\Async;

use \Exception;
use Ac\Async\Json\ReadTrait;
use Ac\Async\Json\WriteTrait;

abstract class Json {

  use ReadTrait;
  use WriteTrait;

  static public function decode($d) {
    $d = json_decode($d, true);
    if(json_last_error() !== 0) {
      throw new Exception(json_last_error_msg());
    }
    return $d;
  }

  static public function encode($d, $pretty = false) {
    $d = json_encode($d, ($pretty ? JSON_PRETTY_PRINT : 0) | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if(json_last_error() !== 0) {
      throw new Exception(json_last_error_msg());
    }
    return $d;
  }

}
