<?php

namespace Ac\Async\Stream;

use \SplFixedArray;
use \SplQueue;
use Ac\Async\Async;
use Ac\Async\Select;
use Ac\Async\EventEmitterTrait;

class Reader {

  use EventEmitterTrait;

  protected $stream;
  protected $select;
  protected $onReadable;

  public function __construct($stream) {
    $this->stream = $stream;
    $this->select =& Async::getSelect();
    $this->onReadable = function() {
      $this->readable();
    };
    $this->select->addCallbackReadable($this->onReadable, $stream);
  }

  public function __destruct() {
    $this->destroy();
  }

  protected function readable() {
    // if(!is_resource($this->stream)) return;
    $buffer = fread($this->stream, Select::CHUNK_SIZE);
    if($buffer === '') {
      if(feof($this->stream)) {
        $this->emit('end');
        fclose($this->stream);
        $this->emit('close');
        $this->destroy();
      }
      return;
    }
    $this->emit('data', $buffer);
  }

  public function destroy() {
    if(isset($this->stream)) {
      unset(self::$readers[Select::streamId($this->stream)]);
    }
    if(isset($this->select)) {
      $this->select->removeCallbackReadable($this->onReadable, $this->stream);
    }
    unset($this->stream);
    unset($this->select);
    unset($this->onReadable);
    $this->removeAllListeners();
  }

  // Static

  static protected $readers = [];

  static public function &factory($stream) {
    $id = Select::streamId($stream);
    if(!isset(self::$readers[$id])) {
      self::$readers[$id] = new Reader($stream);
    }
    return self::$readers[$id];
  }
}
