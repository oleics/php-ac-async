<?php

namespace Ac\Async;

use \Exception;
use Ac\Async\KeyValueStorage;

class Select {

  const CHUNK_SIZE        = 8192;
  const READ_BUFFER_SIZE  = 0;
  const WRITE_BUFFER_SIZE = 0;

  const IDLE   = 0;
  const ACTIVE = 1;
  const DONE   = 3;

  private $readables   = [];
  private $writables   = [];
  private $exceptables = [];

  private $timeoutSeconds      = 0;
  private $timeoutMicroseconds = 200000;

  private $callbacksReadable;
  private $callbacksWritable;
  private $callbacksExceptable;

  private $state = self::IDLE;
  private $idleCallbacks = [];
  private $activeCallbacks = [];
  private $doneCallbacks = [];

  static private $NULL = null;
  static private $TRUE = true;
  static private $singleton;

  // Constructor

  public function __construct($timeoutSeconds = 0, $timeoutMicroseconds = 200000) {
    $this->timeoutSeconds      = (int) $timeoutSeconds;
    $this->timeoutMicroseconds = (int) $timeoutMicroseconds;

    $this->callbacksReadable   = new KeyValueStorage();
    $this->callbacksWritable   = new KeyValueStorage();
    $this->callbacksExceptable = new KeyValueStorage();
  }

  // Singleton

  // static public function &Singleton() {
  //   if(!isset(self::$singleton)) {
  //     self::$singleton = new StreamSelect();
  //   }
  //   return self::$singleton;
  // }

  // Select

  public function select() {
    $this->removeAllClosedStreams();

    // Prepare
    $read = empty($this->readables) ? null : array_values($this->readables);
    $write = empty($this->writables) ? null : array_values($this->writables);
    $except = empty($this->exceptables) ? null : array_values($this->exceptables);

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
      if($this->state !== self::IDLE) {
        $this->state = self::IDLE;
        $this->execIdleCallbacks();
      }
    } else {
      // Something did happen
      if($this->state !== self::ACTIVE) {
        $this->state = self::ACTIVE;
        $this->execActiveCallbacks();
      }

      if(isset($read)) {
        foreach($read as &$stream) {
          $this->execCallbacksReadable($stream);
          $this->removeStreamIfClosed($stream);
        }
      }
      if(isset($write)) {
        foreach($write as &$stream) {
          $this->execCallbacksWritable($stream);
          $this->removeStreamIfClosed($stream);
        }
      }
      if(isset($except)) {
        foreach($except as &$stream) {
          $this->execCallbacksExceptable($stream);
          $this->removeStreamIfClosed($stream);
        }
      }

    }

    if($this->hasStreams()) {
      return true;
    }

    $this->state = self::DONE;
    $this->execDoneCallbacks();

    // done-callbacks can add streams too
    if($this->hasStreams()) {
      $this->state = self::IDLE;
      return true;
    }

    return false;
  }

  private function hasStreams() {
    return !empty($this->readables) || !empty($this->writables) || !empty($this->exceptables);
  }

  private function removeAllClosedStreams() {
    foreach($this->readables as &$stream) {
      if(!is_resource($stream)) {
        $this->removeReadable($stream);
      }
    }
    foreach($this->writables as &$stream) {
      if(!is_resource($stream)) {
        $this->removeWritable($stream);
      }
    }
    foreach($this->exceptables as &$stream) {
      if(!is_resource($stream)) {
        $this->removeExceptable($stream);
      }
    }
  }

  private function removeStreamIfClosed(&$stream) {
    if(!is_resource($stream)) {
      $this->removeReadable($stream);
      $this->removeWritable($stream);
      $this->removeExceptable($stream);
      return true;
    }
    return false;
  }

  //////////////
  // Callbacks

  // Callbacks: Idle

  public function addIdleCallback(callable $callback) {
    if(in_array($callback, $this->idleCallbacks)) return false;
    array_push($this->idleCallbacks, $callback);
    return true;
  }

  public function removeIdleCallback(callable $callback) {
    $index = array_search($callback, $this->idleCallbacks);
    if($index === false) return false;
    array_splice($this->idleCallbacks, $index, 1);
    return true;
  }

  private function execIdleCallbacks() {
    foreach($this->idleCallbacks as $callback) {
      call_user_func_array($callback, [&$this]);
    }
  }

  // Callbacks: Active

  public function addActiveCallback(callable $callback) {
    if(in_array($callback, $this->activeCallbacks)) return false;
    array_push($this->activeCallbacks, $callback);
    return true;
  }

  public function removeActiveCallback(callable $callback) {
    $index = array_search($callback, $this->activeCallbacks);
    if($index === false) return false;
    array_splice($this->activeCallbacks, $index, 1);
    return true;
  }

  private function execActiveCallbacks() {
    foreach($this->activeCallbacks as $callback) {
      call_user_func_array($callback, [&$this]);
    }
  }

  // Callbacks: Done

  public function addDoneCallback(callable $callback) {
    if(in_array($callback, $this->doneCallbacks)) return false;
    array_push($this->doneCallbacks, $callback);
    return true;
  }

  public function removeDoneCallback(callable $callback) {
    $index = array_search($callback, $this->doneCallbacks);
    if($index === false) return false;
    array_splice($this->doneCallbacks, $index, 1);
    return true;
  }

  private function execDoneCallbacks() {
    foreach($this->doneCallbacks as $callback) {
      call_user_func_array($callback, [&$this]);
    }
  }

  // Callbacks: Readable

  public function addCallbackReadable(callable $callback, $stream = null) {
    if(isset($stream) && $stream !== true) $this->addReadable($stream);
    return $this->callbacksReadable->add($callback, $stream);
  }

  public function removeCallbackReadable(callable $callback) {
    $stream = $this->callbacksReadable->getValue($callback);
    $result = $this->callbacksReadable->remove($callback);
    if(isset($stream) && ! $this->callbacksReadable->valueExists($stream)) {
      $this->removeReadable($stream);
    }
    return $result;
  }

  private function execCallbacksReadable(&$stream) {
    $found = false;
    foreach($this->callbacksReadable->getKeysOfValue($stream) as $callback) {
      call_user_func_array($callback, [&$stream]);
      $found = true;
    }
    if(!$found) {
      foreach($this->callbacksReadable->getKeysOfValue(self::$NULL) as $callback) {
        call_user_func_array($callback, [&$stream]);
      }
    }
    foreach($this->callbacksReadable->getKeysOfValue(self::$TRUE) as $callback) {
      call_user_func_array($callback, [&$stream]);
    }
  }

  // Callbacks: Writable

  public function addCallbackWritable(callable $callback, $stream = null) {
    if(isset($stream) && $stream !== true) $this->addWritable($stream);
    return $this->callbacksWritable->add($callback, $stream);
  }

  public function removeCallbackWritable(callable $callback) {
    $stream = $this->callbacksWritable->getValue($callback);
    $result = $this->callbacksWritable->remove($callback);
    if(isset($stream) && ! $this->callbacksWritable->valueExists($stream)) {
      $this->removeWritable($stream);
    }
    return $result;
  }

  private function execCallbacksWritable(&$stream) {
    $found = false;
    foreach($this->callbacksWritable->getKeysOfValue($stream) as $callback) {
      call_user_func_array($callback, [&$stream]);
      $found = true;
    }
    if(!$found) {
      foreach($this->callbacksWritable->getKeysOfValue(self::$NULL) as $callback) {
        call_user_func_array($callback, [&$stream]);
      }
    }
    foreach($this->callbacksWritable->getKeysOfValue(self::$TRUE) as $callback) {
      call_user_func_array($callback, [&$stream]);
    }
  }

  // Callbacks: Exceptable

  public function addCallbackExceptable(callable $callback, $stream = null) {
    if(isset($stream) && $stream !== true) $this->addExceptable($stream);
    return $this->callbacksExceptable->add($callback, $stream);
  }

  public function removeCallbackExceptable(callable $callback) {
    $stream = $this->callbacksExceptable->getValue($callback);
    $result = $this->callbacksExceptable->remove($callback);
    if(isset($stream) && ! $this->callbacksExceptable->valueExists($stream)) {
      $this->removeExceptable($stream);
    }
    return $result;
  }

  private function execCallbacksExceptable(&$stream) {
    $found = false;
    foreach($this->callbacksExceptable->getKeysOfValue($stream) as &$callback) {
      call_user_func_array($callback, [&$stream]);
      $found = true;
    }
    if(!$found) {
      foreach($this->callbacksExceptable->getKeysOfValue(self::$NULL) as &$callback) {
        call_user_func_array($callback, [&$stream]);
      }
    }
    foreach($this->callbacksExceptable->getKeysOfValue(self::$TRUE) as &$callback) {
      call_user_func_array($callback, [&$stream]);
    }
  }

  ////////////
  // Streams

  // Streams: Readable

  public function addReadable($stream) {
    if(in_array($stream, $this->readables)) return false;
    array_push($this->readables, $stream);
    stream_set_blocking($stream, false);
    stream_set_read_buffer($stream, self::READ_BUFFER_SIZE);
    stream_set_chunk_size($stream, self::CHUNK_SIZE);
    return true;
  }

  public function removeReadable($stream) {
    $index = array_search($stream, $this->readables);
    if($index === false) return false;
    array_splice($this->readables, $index, 1);
    $this->callbacksReadable->remove($stream);
  }

  public function numReadables() {
    return count($this->readables);
  }

  // Streams: Writable

  public function addWritable($stream) {
    if(in_array($stream, $this->writables)) return false;
    array_push($this->writables, $stream);
    stream_set_blocking($stream, false);
    stream_set_write_buffer($stream, self::READ_BUFFER_SIZE);
    return true;
  }

  public function removeWritable($stream) {
    $index = array_search($stream, $this->writables);
    if($index === false) return false;
    array_splice($this->writables, $index, 1);
    $this->callbacksWritable->remove($stream);
    return true;
  }

  /**
   * @return int
   */
  public function numWritables() {
    return count($this->writables);
  }

  // Streams: Exceptable

  /**
   * @return bool
   */
  public function addExceptable($stream) {
    if(in_array($stream, $this->exceptables)) return false;
    array_push($this->exceptables, $stream);
    stream_set_blocking($stream, false);
    stream_set_read_buffer($stream, self::READ_BUFFER_SIZE);
    stream_set_chunk_size($stream, self::CHUNK_SIZE);
    return true;
  }

  /**
   * @return bool
   */
  public function removeExceptable($stream) {
    $index = array_search($stream, $this->exceptables);
    if($index === false) return false;
    array_splice($this->exceptables, $index, 1);
    $this->callbacksExceptable->remove($stream);
    return true;
  }

  /**
   * @return int
   */
  public function numExceptables() {
    return count($this->exceptables);
  }

}
