<?php

namespace Ac\Async\Kernel;

use \LogicException;

trait LogTrait {
  static public $SUCCESSIVE_DRIFTS_WARN  = 20;
  static public $SUCCESSIVE_DRIFTS_FATAL = 200;

  protected $log;

  public function setLog($log) {
    $this->log = $log;
  }

  public function &getLog() {
    return $this->log;
  }

  public function frameInfos() {
    static $tooSlow = false;
    static $tooFast = false;

    if($this->frame == 1) {
      $tooSlow = false;
      $tooFast = false;
      $this->log->debug('Kernel Start: mode %s, framerate %f', $this->mode, $this->framerate);
    }

    if($this->successiveNegativeTimeDrifts == self::$SUCCESSIVE_DRIFTS_FATAL) {
      $this->log->fatal("$this->successiveNegativeTimeDrifts successive drifts, exiting... $this->timeDrift");
    } else if($this->successiveNegativeTimeDrifts == self::$SUCCESSIVE_DRIFTS_WARN) {
      $this->log->warn("$this->successiveNegativeTimeDrifts successive drifts. $this->timeDrift");
    } else {
      assert(($tooSlow && $tooFast) === false, 'Kernel can never be too-fast and too-slow at the same time.');

      if($this->timeDrift <= -1*$this->framerate) {
        if(!$tooSlow) {
          $tooSlow = 1;
          $frames = floor($this->timeDrift / $this->framerate);
          $this->log->info("$frames frames behind. timeDrift: $this->timeDrift", 'TOO SLOW');
        } else {
          $tooSlow++;
        }
        return;
      }

      if($tooSlow) {
        $this->log->info("Synchronized after $tooSlow frames, timeDrift: $this->timeDrift", 'TOO SLOW');
        $tooSlow = false;
        return;
      }

      if($this->timeDrift >= $this->framerate) {
        if(!$tooFast) {
          $tooFast = 1;
          $frames = floor($this->timeDrift / (-1*$this->framerate));
          $this->log->info("$frames frames ahead. timeDrift: $this->timeDrift", 'TOO FAST');
        } else {
          $tooFast++;
        }
        return;
      }

      if($tooFast) {
        $this->log->info("Synchronized after $tooFast frames, timeDrift: $this->timeDrift", 'TOO FAST');
        $tooFast = false;
        return;
      }

      if($this->frame % 2 === 0) $this->log->trace();

    }
  }

}
