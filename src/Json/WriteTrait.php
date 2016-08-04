<?php

namespace Ac\Async\Json;

use Ac\Async\Stream;
use Ac\Async\Json;

trait WriteTrait {

  static function write($stream) {
    $streamWrite = Stream::write($stream);
    $write = function($data, callable $callback = null) use(&$streamWrite) {
      return $streamWrite(Json::encode($data)."\n", $callback);
    };
    return $write;
  }

}
