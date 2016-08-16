<?php

namespace Ac\Async\Stream;

use \Exception;
use \SplFixedArray;
use \SplQueue;
use Ac\Async\Async;
use Ac\Async\Select;

class Writer {

  protected $stream;
  protected $select;
  protected $buffer;
  protected $current;
  protected $onWritable;

  public function __construct($stream) {
    $streamId = Select::streamId($stream);
    if(isset(self::$factoryInstances[$streamId])) {
      throw new Exception('A writer for that stream already exists. You can use Writer::factory($stream) to resolve this.');
    }
    self::$factoryInstances[$streamId] = $this;

    $this->stream = $stream;
    $this->select =& Async::getSelect();
    $this->buffer = new SplQueue();
    $this->onWritable = function() {
      $this->writeable();
    };
  }

  public function __destruct() {
    $this->destroy();
  }

  public function write($data, callable $callback = null) {
    $d = new SplFixedArray(4);
    if($this->current) {
      $d[0] = $data;
      $d[1] = $callback;
      $this->buffer->enqueue($d);
      return;
    }
    $d[0] = $data;
    $d[1] = $callback;
    $d[2] = strlen($data);
    $d[3] = 0;
    $this->current = $d;
    $this->select->addCallbackWritable($this->onWritable, $this->stream);
  }

  protected function writeable() {
    // if(!is_resource($this->stream)) return;
    $this->current[3] = $this->current[3] + fwrite($this->stream, substr($this->current[0], $this->current[3]));
    if($this->current[2] === $this->current[3]) {
      if($this->current[1] !== null) {
        call_user_func($this->current[1]);
      }
      if($this->buffer->isEmpty()) {
        $this->current = false;
        $this->select->removeCallbackWritable($this->onWritable, $this->stream);
        return;
      }
      $this->current = $this->buffer->dequeue();
      $this->current[2] = strlen($this->current[0]);
      $this->current[3] = 0;
    }
  }

  public function destroy() {
    if(isset($this->stream)) {
      unset(self::$factoryInstances[Select::streamId($this->stream)]);
    }
    if(isset($this->select)) {
      $this->select->removeCallbackWritable($this->onWritable, $this->stream);
    }
    unset($this->stream);
    unset($this->select);
    unset($this->buffer);
    unset($this->current);
    unset($this->onWritable);
  }

  // Static

  static protected $factoryInstances = [];

  static public function &factory($stream) {
    $id = Select::streamId($stream);
    if(!isset(self::$factoryInstances[$id])) {
      self::$factoryInstances[$id] = new Writer($stream);
    }
    return self::$factoryInstances[$id];
  }
}
