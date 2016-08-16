<?php

namespace Ac\Async\Stream;

use \SplFixedArray;
use Ac\Async\Stream;
use Ac\Async\Select;

class Pipe {

  protected $stream;
  protected $unbindRead;
  protected $pipes = [];
  protected $onDestroy;
  protected $closePipes = true;

  public function __construct($stream, callable $onDestroy = null, $closePipes = true) {
    $this->stream = $stream;
    $this->onDestroy = $onDestroy;
    $this->closePipes = (bool) $closePipes;
  }

  protected function startReading() {
    if(isset($this->unbindRead)) return;
    $this->unbindRead = Stream::read($this->stream, function($d) {
      if($d === null) {
        $this->destroy();
        return;
      }
      foreach($this->pipes as &$p) {
        call_user_func($p[1], $d);
      }
    });
  }

  protected function stopReading() {
    if(!isset($this->unbindRead)) return;
    call_user_func($this->unbindRead);
    unset($this->unbindRead);
  }

  public function add($writable) {
    $streamId = Select::streamId($writable);
    if(isset($this->pipes[$streamId])) return $this;
    $p = new SplFixedArray(2);
    $p[0] = $writable;
    $p[1] = Stream::write($writable);
    $this->pipes[$streamId] = $p;
    $this->startReading();
    return $this;
  }

  public function remove($writable) {
    $streamId = Select::streamId($writable);
    if(!isset($this->pipes[$streamId])) return $this;
    unset($this->pipes[$streamId]);
    if(empty($this->pipes)) $this->stopReading();
    return $this;
  }

  public function destroy() {
    if($this->closePipes) {
      $this->closePipes();
    }
    if(isset($this->unbindRead)) {
      call_user_func($this->unbindRead);
    }
    if(isset($this->onDestroy)) {
      call_user_func($this->onDestroy);
    }
    unset($this->stream);
    unset($this->unbindRead);
    unset($this->pipes);
    unset($this->onDestroy);
  }

  protected function closePipes() {
    while(($p = array_pop($this->pipes)) !== null) {
      fclose($p[0]);
    }
  }

  // Static

  static protected $factoryInstances = [];

  static public function &factory($stream) {
    $streamId = Select::streamId($stream);
    if(!isset(self::$factoryInstances[$streamId])) {
      self::$factoryInstances[$streamId] = new Pipe($stream);
    }
    return self::$factoryInstances[$streamId];
  }

}
