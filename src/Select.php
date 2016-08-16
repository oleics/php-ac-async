<?php

namespace Ac\Async;

use \Exception;
use Ac\Async\EventEmitterTrait;

/**
 * Manager for select() system calls.
 *
 * @triggers idle
 * @triggers active
 * @triggers done
 *
 * @triggers add-readable $stream
 * @triggers readable $stream
 * @triggers readable-[streamid] $stream
 * @triggers remove-readable $stream
 *
 * @triggers add-writable $stream
 * @triggers writable $stream
 * @triggers writable-[streamid] $stream
 * @triggers remove-writable $stream
 *
 * @triggers add-exceptable $stream
 * @triggers exceptable $stream
 * @triggers exceptable-[streamid] $stream
 * @triggers remove-exceptable $stream
 *
 * @triggers invalid $stream
 * @triggers invalid-[streamid] $stream
 */
class Select {

  use EventEmitterTrait;

  const CHUNK_SIZE        = 8192;
  const READ_BUFFER_SIZE  = 0;
  const WRITE_BUFFER_SIZE = 0;

  const STATE_IDLE   = 0;
  const STATE_ACTIVE = 1;
  const STATE_DONE   = 3;

  protected $readables   = [];
  protected $writables   = [];
  protected $exceptables = [];

  protected $timeoutSeconds      = 0;
  protected $timeoutMicroseconds = 200000;

  protected $state = self::STATE_IDLE;
  protected $idleCallbacks = [];
  protected $activeCallbacks = [];
  protected $doneCallbacks = [];

  // Constructor

  public function __construct($timeoutSeconds = 0.2) {
    $this->setTimeout($timeoutSeconds);
  }

  public function setTimeout($timeoutSeconds) {
    $this->timeoutSeconds      = (int) floor($timeoutSeconds);
    $this->timeoutMicroseconds = (int) round(($timeoutSeconds-$this->timeoutSeconds) * 1e6);
  }

  // Select

  /**
   * @return bool
   */
  public function select() {
    $this->removeAllClosedStreams();

    // Prepare
    $read = empty($this->readables) ? null : $this->readables;
    $write = empty($this->writables) ? null : $this->writables;
    $except = empty($this->exceptables) ? null : $this->exceptables;

    // Select
    $numChangedStreams = stream_select(
      $read, $write, $except,
      $this->timeoutSeconds, $this->timeoutMicroseconds
    );

    // Handle
    if(false === $numChangedStreams) {
      // Error
      throw new Exception('Socket internal error');
    } else if(0 === $numChangedStreams) {
      // Timeout kicked in, nothing did happen
      if($this->state !== self::STATE_IDLE) {
        $this->state = self::STATE_IDLE;
        $this->emit('idle');
      }
    } else {
      // Something did happen
      if($this->state !== self::STATE_ACTIVE) {
        $this->state = self::STATE_ACTIVE;
        $this->emit('active');
      }

      if(isset($read)) {
        foreach($read as &$stream) {
          $this->emit('readable', $stream);
          $this->emit('readable-'.self::streamId($stream), $stream);
          $this->validateStream($stream);
        }
      }
      if(isset($write)) {
        foreach($write as &$stream) {
          $this->emit('writable', $stream);
          $this->emit('writable-'.self::streamId($stream), $stream);
          $this->validateStream($stream);
        }
      }
      if(isset($except)) {
        foreach($except as &$stream) {
          $this->emit('exceptable', $stream);
          $this->emit('exceptable-'.self::streamId($stream), $stream);
          $this->validateStream($stream);
        }
      }
    }

    if($this->hasStreams()) {
      return true;
    }

    $this->state = self::STATE_DONE;
    $this->emit('done');

    // done-callbacks can add streams too
    if($this->hasStreams()) {
      $this->state = self::STATE_IDLE;
      return true;
    }

    return false;
  }

  protected function hasStreams() {
    return !empty($this->readables) || !empty($this->writables) || !empty($this->exceptables);
  }

  protected function removeAllClosedStreams() {
    foreach($this->readables as &$stream) {
      $this->validateStream($stream);
    }
    foreach($this->writables as &$stream) {
      $this->validateStream($stream);
    }
    foreach($this->exceptables as &$stream) {
      $this->validateStream($stream);
    }
  }

  protected function validateStream(&$stream) {
    if(!is_resource($stream)) {
      $this->removeReadable($stream);
      $this->removeWritable($stream);
      $this->removeExceptable($stream);

      $this->emit('invalid', $stream);
      $id = self::streamId($stream);
      $this->emit('invalid-'.$id, $stream);
      $this->removeAllListeners('invalid-'.$id);
    }
  }

  //////////////
  // Callbacks

  // Callbacks: Readable

  public function addCallbackReadable(callable $callback, $stream = null) {
    if(isset($stream)) {
      $this->addReadable($stream);
      $this->on('readable-'.self::streamId($stream), $callback);
    } else {
      $this->on('readable', $callback);
    }
  }

  public function removeCallbackReadable(callable $callback, $stream = null) {
    if(isset($stream)) {
      $event = 'readable-'.self::streamId($stream);
      $this->removeListener($event, $callback);
      if(0 === Select::listenerCount($this, $event)) {
        $this->removeReadable($stream);
      }
    } else {
      $this->removeListener('readable', $callback);
    }
  }

  // Callbacks: Writable

  public function addCallbackWritable(callable $callback, $stream = null) {
    if(isset($stream)) {
      $this->addWritable($stream);
      $this->on('writable-'.self::streamId($stream), $callback);
    } else {
      $this->on('writable', $callback);
    }
  }

  public function removeCallbackWritable(callable $callback, $stream = null) {
    if(isset($stream)) {
      $event = 'writable-'.self::streamId($stream);
      $this->removeListener($event, $callback);
      if(0 === Select::listenerCount($this, $event)) {
        $this->removeWritable($stream);
      }
    } else {
      $this->removeListener('writable', $callback);
    }
  }

  // Callbacks: Exceptable

  public function addCallbackExceptable(callable $callback, $stream = null) {
    if(isset($stream)) {
      $this->addExceptable($stream);
      $this->on('exceptable-'.self::streamId($stream), $callback);
    } else {
      $this->on('exceptable', $callback);
    }
  }

  public function removeCallbackExceptable(callable $callback, $stream = null) {
    if(isset($stream)) {
      $event = 'exceptable-'.self::streamId($stream);
      $this->removeListener($event, $callback);
      if(0 === Select::listenerCount($this, $event)) {
        $this->removeExceptable($stream);
      }
    } else {
      $this->removeListener('exceptable', $callback);
    }
  }

  // Callbacks: Invalid

  public function addCallbackInvalid(callable $callback, $stream = null) {
    if(isset($stream)) {
      if(!$this->knowsStream($stream)) {
        throw new Exception('Unknown stream.');
      }
      $this->on('invalid-'.self::streamId($stream), $callback);
    } else {
      $this->on('invalid', $callback);
    }
  }

  public function removeCallbackInvalid(callable $callback, $stream = null) {
    if(isset($stream)) {
      $event = 'invalid-'.self::streamId($stream);
      $this->removeListener($event, $callback);
    } else {
      $this->removeListener('invalid', $callback);
    }
  }

  ////////////
  // Streams

  /**
   * @return bool
   */
  public function knowsStream($stream) {
    if(in_array($stream, $this->readables)) return true;
    if(in_array($stream, $this->writables)) return true;
    if(in_array($stream, $this->exceptables)) return true;
    return false;
  }

  // Streams: Readable

  public function addReadable($stream) {
    if(in_array($stream, $this->readables)) return;
    array_push($this->readables, $stream);
    stream_set_blocking($stream, false);
    stream_set_read_buffer($stream, self::READ_BUFFER_SIZE);
    stream_set_chunk_size($stream, self::CHUNK_SIZE);
    $this->emit('add-readable', $stream);
  }

  public function removeReadable($stream) {
    $index = array_search($stream, $this->readables);
    if($index === false) return;
    array_splice($this->readables, $index, 1);
    $this->removeAllListeners('readable-'.self::streamId($stream));
    $this->emit('remove-readable', $stream);
  }

  /**
   * @return int
   */
  public function numReadables() {
    return count($this->readables);
  }

  // Streams: Writable

  public function addWritable($stream) {
    if(in_array($stream, $this->writables)) return;
    array_push($this->writables, $stream);
    stream_set_blocking($stream, false);
    stream_set_write_buffer($stream, self::READ_BUFFER_SIZE);
    $this->emit('add-writable', $stream);
  }

  public function removeWritable($stream) {
    $index = array_search($stream, $this->writables);
    if($index === false) return;
    array_splice($this->writables, $index, 1);
    $this->removeAllListeners('writable-'.self::streamId($stream));
    $this->emit('remove-writable', $stream);
  }

  /**
   * @return int
   */
  public function numWritables() {
    return count($this->writables);
  }

  // Streams: Exceptable

  public function addExceptable($stream) {
    if(in_array($stream, $this->exceptables)) return;
    array_push($this->exceptables, $stream);
    stream_set_blocking($stream, false);
    stream_set_read_buffer($stream, self::READ_BUFFER_SIZE);
    stream_set_chunk_size($stream, self::CHUNK_SIZE);
    $this->emit('add-exceptable', $stream);
  }

  public function removeExceptable($stream) {
    $index = array_search($stream, $this->exceptables);
    if($index === false) return;
    array_splice($this->exceptables, $index, 1);
    $this->removeAllListeners('exceptable-'.self::streamId($stream));
    $this->emit('remove-exceptable', $stream);
  }

  /**
   * @return int
   */
  public function numExceptables() {
    return count($this->exceptables);
  }

  ///////////
  // Static

  /**
   * @return int|float|string
   */
  static public function streamId($stream) {
    return intval($stream);
  }

}
