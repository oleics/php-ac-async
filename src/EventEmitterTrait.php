<?php

namespace Ac\Async;

use \Exception;

/**  */
trait EventEmitterTrait {

  static public $defaultMaxListeners = 10;

  protected $_listeners;
  protected $_maxListeners;

  /**  */
  public function emit($event /*, $arg1, ...,  $argX*/) {
    if(!isset($this->_listeners[$event])) {
      if($event === 'error') {
        $err = func_get_arg(1);
        if(is_scalar($err)) {
          $err = new Exception($err);
        }
        throw $err;
      }
      return $this;
    }

    $args = array_slice(func_get_args(), 1);
    $listeners = array_reverse($this->_listeners[$event]);
    while(($d = array_pop($listeners)) !== null) {
      if($d[1] === true) {
        array_splice($this->_listeners[$event], array_search($d, $this->_listeners[$event], true), 1);
      }
      call_user_func_array($d[0], $args);
    }

    return $this;
  }

  /**  */
  protected function _addListener($event, callable $listener, $once = false) {
    if(!isset($this->_listeners)) $this->_listeners = [];
    if(!isset($this->_listeners[$event])) $this->_listeners[$event] = [];

    if(isset($this->_listeners['newListener'])) {
      $this->emit('newListener', $event, $listener);
    }

    $this->_listeners[$event][] = [$listener, $once];
    if(!isset($this->_maxListeners)) {
      $this->_maxListeners = self::$defaultMaxListeners;
    }

    if($this->_maxListeners !== 0 && count($this->_listeners[$event]) > $this->_maxListeners) {
      throw new Exception('Possible memory-leak detected! More than '.$this->_maxListeners.' listeners added to event "'.$event.'".');
    }
  }

  /**  */
  public function addListener($event, callable $listener) {
    $this->_addListener($event, $listener, false);
    return $this;
  }

  /**  */
  public function on($event, callable $listener) {
    $this->_addListener($event, $listener, false);
    return $this;
  }

  /**  */
  public function once($event, callable $listener) {
    $this->_addListener($event, $listener, true);
    return $this;
  }

  /**  */
  public function removeListener($event, callable $listener) {
    if(isset($this->_listeners['removeListener'])) {
      $this->emit('removeListener', $event, $listener);
    }

    if(!isset($this->_listeners[$event])) return $this;

    $listeners = &$this->_listeners[$event];
    $len = count($listeners);
    for($index=0; $index<$len; $index++) {
      $d = &$listeners[$index];
      if($d[0] === $listener) {
        array_splice($listeners, $index, 1);
        if(empty($listeners)) {
          unset($this->_listeners[$event]);
        }
        return $this;
      }
    }

    return $this;
  }

  /**  */
  public function removeAllListeners($event = null) {
    if(!isset($event)) {
      if(!isset($this->_listeners)) return $this;
      unset($this->_listeners);
      return $this;
    }
    if(!isset($this->_listeners[$event])) return $this;
    unset($this->_listeners[$event]);
    return $this;
  }

  /**  */
  public function setMaxListeners($maxListeners) {
    $this->_maxListeners = $_maxListeners;
    return $this;
  }

  /**  */
  public function listeners($event) {
    if(!isset($this->_listeners[$event])) return false;
    return array_map(function($d){return $d[0];}, $this->_listeners[$event]);
  }

  // Static

  /**  */
  static public function listenerCount($emitter, $event) {
    if(!isset($emitter->_listeners[$event])) return 0;
    return count($emitter->_listeners[$event]);
  }

}
