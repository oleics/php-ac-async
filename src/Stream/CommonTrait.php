<?php

namespace Ac\Async\Stream;

use Ac\Async\Stream\ThroughWrapper;

ThroughWrapper::register();

trait CommonTrait {

  static public function openProcess($cmd) {
    return popen($cmd, 'rb');
  }

  static public function openReadable($url = 'php://temp') {
    return fopen($url, 'rb');
  }

  static public function openWritable($url = 'php://temp') {
    return fopen($url, 'wb');
  }

  static public function openDuplex($url = 'php://temp') {
    return fopen($url, 'w+b');
  }

  static public function openThrough($url = 'php://temp') {
    return fopen(ThroughWrapper::DEFAULT_PROTOCOL.'://'.$url, 'w+b');
  }

}
