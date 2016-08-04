<?php

namespace Ac\Async;

use Ac\Async\Stream;

class Process {

  const SIGNAL_SIGTERM = 15;

  static protected $descriptorspec = array(
     0 => array('pipe', 'r'),  // stdin is a pipe that the child will read from
     1 => array('pipe', 'w'),  // stdout is a pipe that the child will write to
     2 => array('pipe', 'w')   // stderr is a file to write to
  );

  protected $proc;
  protected $writer;

  // Streams
  public $stdin;
  public $stdout;
  public $stderr;

  // Status
  public $command;
  public $pid;
  public $running;
  public $signaled;
  public $stopped;
  public $exitcode;
  public $termsig;
  public $stopsig;

  public function __construct($cmd) {
    $this->proc = proc_open($cmd, self::$descriptorspec, $pipes);

    $this->stdin = $pipes[0];
    $this->stdout = $pipes[1];
    $this->stderr = $pipes[2];

    $this->getStatus();
  }

  protected function closePipes() {
    if(is_resource($this->stdin)) fclose($this->stdin);
    if(is_resource($this->stdout)) fclose($this->stdout);
    if(is_resource($this->stderr)) fclose($this->stderr);
    unset($this->stdin);
    unset($this->stdout);
    unset($this->stderr);
  }

  protected function getStatus() {
    if(!is_resource($this->proc)) return false;

    $status = proc_get_status($this->proc);

    $this->command  = $status['command'];
    $this->pid      = $status['pid'];
    $this->running  = $status['running'];
    $this->signaled = $status['signaled'];
    $this->stopped  = $status['stopped'];
    if($status['exitcode'] > -1) $this->exitcode = $status['exitcode'];
    $this->termsig  = $status['termsig'];
    $this->stopsig  = $status['stopsig'];

    return $status;
  }

  public function kill($signal = self::SIGNAL_SIGTERM) {
    $this->closePipes();
    proc_terminate($this->proc, $signal);
    $this->getStatus();
    unset($this->writer);
    unset($this->proc);
    return $this->exitcode;
  }

  public function close() {
    $this->closePipes();

    $status = proc_close($this->proc);
    $this->getStatus();

    unset($this->stdin);
    unset($this->stdout);
    unset($this->stderr);
    unset($this->writer);
    unset($this->proc);

    return $status;
  }

  // Convenience

  public function read(callable $callback) {
    $numOfNulls = 0;
    $onAnyData = function($d) use(&$callback, &$numOfNulls) {
      if($d === null) {
        if(++$numOfNulls === 2) {
          async($callback, 10, [null]);
        }
        return;
      }
      async($callback, 10, [$d]);
    };

    $unbindStdoutReader = $this->readStdout($onAnyData);
    $unbindStderrReader = $this->readStderr($onAnyData);

    $unbind = function() use(&$unbindStdoutReader, &$unbindStderrReader) {
      $unbindStdoutReader();
      $unbindStderrReader();
    };

    return $unbind;
  }

  public function readStdout(callable $callback) {
    return Stream::read($this->stdout, $callback);
  }

  public function readStderr(callable $callback) {
    return Stream::read($this->stderr, $callback);
  }

  public function write($data, callable $callback = null) {
    if(!isset($this->writer)) {
      $this->writer = Stream::writer($this->stdin);
    }
    return $this->writer($data, $callback);
  }

  // Convenience

  static public function spawn($cmd) {
    return new Process($cmd);
  }

}
