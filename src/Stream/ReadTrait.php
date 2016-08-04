<?php

namespace Ac\Async\Stream;

use Ac\Async\Async;
use Ac\Async\Select;
use Ac\Async\StringParser;

trait ReadTrait {

  static public function read($stream, callable $callback) {
    $select =& Async::getSelect();

    $readableCallback = function(&$stream) use(&$callback) {
      if(!is_resource($stream)) return;
      $buffer = fread($stream, Select::CHUNK_SIZE);
      if($buffer === '') {
        if(feof($stream)) {
          fclose($stream);
          async($callback, 10, [null]);
        }
        return;
      }
      async($callback, 10, [$buffer]);
    };

    $select->addCallbackReadable($readableCallback, $stream);

    // unbind-function
    return function() use(&$select, &$stream, &$readableCallback) {
      return $select->removeCallbackReadable($readableCallback, $stream);
    };
  }

  static public function readAndParse($stream, StringParser $parser, callable $callback) {
    $readUnbind;
    $readUnbind = self::read($stream, function($data) use(&$parser, &$callback, &$readUnbind) {
      if($data === null) {
        $readUnbind();
        foreach($parser->end() as $part) {
          async($callback, 10, [$part]);
        }
        async($callback, 10, [null]);
        return;
      }
      foreach($parser->write($data) as $part) {
        async($callback, 10, [$part]);
      }
    });
    return $readUnbind;
  }

  static public function readLines($stream, callable $callback) {
    $parser = new StringParser("\n");
    return self::readAndParse($stream, $parser, $callback);
  }

}
