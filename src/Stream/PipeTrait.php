<?php

namespace Ac\Async\Stream;

use Ac\Async\Stream\Pipe;

trait PipeTrait {

  static public function pipe($source, $target) {
    Pipe::factory($source)->add($target);
  }

  static public function unpipe($source, $target) {
    $pipe =& Pipe::factory($source);
    $pipe->remove($target);
    if(0 === $pipe->size()) {
      $pipe->destroy();
    }
  }

}
