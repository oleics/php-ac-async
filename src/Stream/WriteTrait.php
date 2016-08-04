<?php

namespace Ac\Async\Stream;

use \SplFixedArray;
use \SplQueue;
use Ac\Async\Async;

trait WriteTrait {

  static public function write($stream) {
    $select =& Async::getSelect();

    $queue = new SplQueue();
    $current = false;

    $writableCallback;
    $writableCallback = function(&$stream) use(&$queue, &$current, &$writableCallback, &$select) {
      if(!is_resource($stream)) return;
      $current[3] = $current[3] + fwrite($stream, substr($current[0], $current[3]));
      if($current[2] === $current[3]) {
        if($current[1] !== null) {
          async($current[1], 10, []);
        }
        if($queue->isEmpty()) {
          $current = false;
          $select->removeCallbackWritable($writableCallback, $stream);
          return;
        }
        $current = $queue->dequeue();
        $current[2] = strlen($current[0]);
        $current[3] = 0;
      }
    };

    $write = function($data, callable $callback = null) use(&$queue, &$current, &$unbind, &$select, &$writableCallback, &$stream) {
      $d = new SplFixedArray(4);
      if($current) {
        $d[0] = $data;
        $d[1] = $callback;
        $queue->enqueue($d);
        return;
      }
      $d[0] = $data;
      $d[1] = $callback;
      $d[2] = strlen($data);
      $d[3] = 0;
      $current = $d;
      $select->addCallbackWritable($writableCallback, $stream);
    };


    return $write;
  }

}
