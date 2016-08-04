<?php

namespace Ac\Async\Stream;

use Ac\Async\Process;

trait ProcessTrait {

  static public function spawnProcess($cmd) {
    return Process::spawn($cmd);
  }

}
