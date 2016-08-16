<?php

namespace Ac\Async\Stream;

use \SplFixedArray;
use \SplQueue;
use Ac\Async\Async;
use Ac\Async\Stream\Writer;

trait WriteTrait {

  static public function write($stream) {
    $writer =& Writer::factory($stream);

    $write = function($data, callable $callback = null) use(&$writer) {
      return $writer->write($data, $callback);
    };

    return $write;
  }

}
