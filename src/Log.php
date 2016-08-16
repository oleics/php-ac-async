<?php

namespace Ac\Async;

use \RangeException;
use Ac\Async\Stream;
use Ac\Async\Json;

class Log {

  protected $stream;
  protected $write;

  public function __construct($stream = STDOUT) {
    $this->stream = $stream;
  }

  private function _log($level, array $args) {
    if(!isset($this->write)) {
      $this->write = Json::write($this->stream);
    }
    call_user_func($this->write, [
      'type' => 'log',
      'level' => $level,
      'args' => $args
    ]);
  }

  public function debug() {
    $this->_log('debug', func_get_args());
  }

  public function log() {
    $this->_log('log', func_get_args());
  }

  public function info() {
    $this->_log('info', func_get_args());
  }

  public function warn() {
    $this->beep();
    $this->_log('warn', func_get_args());
  }

  public function fatal() {
    $this->beep();
    $this->beep();
    $this->beep();
    $this->_log('fatal', func_get_args());
    exit(1);
  }

  public function beep() {
    echo "\x07";
    flush();
  }

}
