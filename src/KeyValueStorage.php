<?php

namespace Ac\Async;

use \Exception;

class KeyValueStorage {

  static private $FALSE = true;

  private $keys;
  private $values;

  public function __construct() {
    $this->keys = [];
    $this->values = [];
  }

  private function searchKey(&$key) {
    return array_search($key, $this->keys);
  }

  private function searchValue(&$value) {
    return array_search($value, $this->values);
  }

  // check

  public function keyExists(&$key) {
    return $this->searchKey($key) !== false;
  }

  public function valueExists(&$value) {
    return $this->searchValue($value) !== false;
  }

  // get

  public function getKeys() {
    return $this->keys;
  }

  public function getValues() {
    return $this->values;
  }

  public function &getValue(&$key) {
    $index = $this->searchKey($key);
    if($index === false) return self::$FALSE;
    return $this->values[$index];
  }

  public function getKeysOfValue(&$value) {
    $r = [];
    foreach($this->values as $k => &$v) {
      if($value === $v) {
        array_push($r, $this->keys[$k]);
      }
    }
    return $r;
  }

  // add

  public function add(&$key, &$value) {
    $index = $this->searchKey($key);
    if($index !== false) throw new Exception('Duplicate key.');
    array_push($this->keys, $key);
    array_push($this->values, $value);
    return true;
  }

  // remove

  public function remove(&$keyOrValue) {
    $removed = false;
    if($this->removeKey($keyOrValue)) {
      $removed = true;
    }
    if($this->removeValue($keyOrValue)) {
      $removed = true;
    }
    return $removed;
  }

  public function removeKey(&$key) {
    $index = $this->searchKey($key);
    if($index !== false) {
      array_splice($this->keys, $index, 1);
      array_splice($this->values, $index, 1);
      return true;
    }
    return false;
  }

  public function removeValue(&$value) {
    $removed = false;
    while(($index = $this->searchValue($value)) !== false) {
      array_splice($this->keys, $index, 1);
      array_splice($this->values, $index, 1);
      $removed = true;
    }
    return $removed;
  }
}
