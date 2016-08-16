<?php

namespace Ac\Async\Json;

use \Exception;
use Ac\Async\Stream;
use Ac\Async\Json;

trait ReadTrait {

  static function read($stream, callable $callback, callable $callbackNoJsonData = null) {
    $onLine = function($line) use(&$callback, &$callbackNoJsonData) {
      if($line === null) {
        call_user_func($callback, null);
        return;
      }
      try {
        $d = Json::decode($line);
      } catch(Exception $err) {
        if(isset($callbackNoJsonData)) {
          call_user_func($callbackNoJsonData, $line);
        }
        return;
      }
      call_user_func($callback, $d);
    };
    return Stream::readLines($stream, $onLine);
  }

}
