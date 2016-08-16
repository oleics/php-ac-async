<?php

namespace Ac\Async\Stream;

use Ac\Async\Async;
use Ac\Async\Select;
use Ac\Async\StringParser;
use Ac\Async\Stream\Reader;

trait ReadTrait {

  static public function read($stream, callable $onData) {
    $reader =& Reader::factory($stream);

    $onEnd = function() use(&$onData) {
      call_user_func($onData, null);
    };

    $reader->on('data', $onData);
    $reader->on('end', $onEnd);

    // unbind-function
    return function() use(&$onData, &$onEnd, &$reader) {
      $reader->removeListener('data', $onData);
      $reader->removeListener('end', $onEnd);
    };
  }

  static public function readAndParse($stream, StringParser $parser, callable $callback) {
    $readUnbind;
    $readUnbind = self::read($stream, function($data) use(&$parser, &$callback, &$readUnbind) {
      if($data === null) {
        $readUnbind();
        foreach($parser->end() as $part) {
          call_user_func($callback, $part);
        }
        call_user_func($callback, null);
        return;
      }
      foreach($parser->write($data) as $part) {
        call_user_func($callback, $part);
      }
    });
    return $readUnbind;
  }

  static public function readLines($stream, callable $callback) {
    $parser = new StringParser("\n");
    return self::readAndParse($stream, $parser, $callback);
  }

}
