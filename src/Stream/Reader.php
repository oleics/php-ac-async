<?php

namespace Ac\Async\Stream;

use \Exception;
use Ac\Async\Async;
use Ac\Async\Select;
use Ac\Async\EventEmitterTrait;

/**
 * @triggers data $chunk
 * @triggers end
 * @triggers close
 */
class Reader {

  use EventEmitterTrait;

  protected $stream;
  protected $select;
  protected $onReadable;
  protected $onInvalid;
  protected $endEmitted = false;

  public function __construct($stream) {
    $streamId = Select::streamId($stream);
    if(isset(self::$factoryInstances[$streamId])) {
      throw new Exception('A reader for that stream already exists. You can use Reader::factory($stream) to resolve this.');
    }
    self::$factoryInstances[$streamId] = $this;

    $this->stream = $stream;
    $this->select =& Async::getSelect();
    $this->onReadable = function() {
      $this->readable();
    };
    $this->onInvalid = function() {
      $this->invalid();
    };
    $this->select->addCallbackReadable($this->onReadable, $stream);
    $this->select->addCallbackInvalid($this->onInvalid, $stream);
  }

  public function __destruct() {
    $this->destroy();
  }

  protected function readable() {
    $buffer = fread($this->stream, Select::CHUNK_SIZE);
    if($buffer === '') {
      if(feof($this->stream)) {
        $this->endEmitted = true;
        $this->emit('end');
        if(isset($this->stream)) {
          fclose($this->stream);
        }
      }
      return;
    }
    $this->emit('data', $buffer);
  }

  protected function invalid() {
    if(!$this->endEmitted) {
      $this->emit('end');
    }
    $this->emit('close');
    $this->destroy();
  }

  public function destroy() {
    if(isset($this->stream)) {
      unset(self::$factoryInstances[Select::streamId($this->stream)]);
    }
    if(isset($this->select)) {
      $this->select->removeCallbackReadable($this->onReadable, $this->stream);
      $this->select->removeCallbackInvalid($this->onInvalid, $this->stream);
    }
    unset($this->stream);
    unset($this->select);
    unset($this->onReadable);
    unset($this->onInvalid);
    $this->removeAllListeners();
  }

  /**
   * @return bool
   */
  public function unused() {
    return(
      EventEmitterTrait::listenerCount($this, 'data') === 0
      && EventEmitterTrait::listenerCount($this, 'end') === 0
      && EventEmitterTrait::listenerCount($this, 'close') === 0
    );
  }

  // Static

  static protected $factoryInstances = [];

  static public function &factory($stream) {
    $streamId = Select::streamId($stream);
    if(!isset(self::$factoryInstances[$streamId])) {
      self::$factoryInstances[$streamId] = new Reader($stream);
    }
    return self::$factoryInstances[$streamId];
  }
}
