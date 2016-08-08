<?php

use \SplFixedArray;

function perf_SplQueue($numOfSamples, $maxStackSize, $maxAddValues) {
  echo "SplQueue()\n";
  $stack = [];
  $start = microtime(true);
  for($i=0; $i<$numOfSamples; $i++) {
    $o = new SplQueue();
    for($ii=0; $ii<$maxAddValues; $ii++) {
      $o->enqueue(mt_rand());
    }
    $stack[abs($i % $maxStackSize)] = $o;
  }
  $duration = microtime(true) - $start;
  echo "total  : ".($duration*1000)." ms\n";
  echo "average: ".(($duration / $numOfSamples)*1000)." ms\n";
  echo "\n";
}

function perf_array($numOfSamples, $maxStackSize, $maxAddValues) {
  echo "array()\n";
  $stack = [];
  $start = microtime(true);
  for($i=0; $i<$numOfSamples; $i++) {
    $o = [];
    for($ii=0; $ii<$maxAddValues; $ii++) {
      $o[] = mt_rand();
    }
    $stack[abs($i % $maxStackSize)] = $o;
  }
  $duration = microtime(true) - $start;
  echo "total  : ".($duration*1000)." ms\n";
  echo "average: ".(($duration / $numOfSamples)*1000)." ms\n";
  echo "\n";
}

function perf_SplQueue_dequeue($numOfSamples, $maxStackSize, $maxAddValues) {
  echo "SplQueue() dequeue\n";
  $stack = [];
  $start = microtime(true);
  $o = new SplQueue();
  for($i=0; $i<$numOfSamples; $i++) {
    for($ii=0; $ii<$maxAddValues; $ii++) {
      $o->enqueue(mt_rand());
    }
    while(!$o->isEmpty()) {
      $stack[abs($i % $maxStackSize)] = $o->dequeue();
    }
  }
  $duration = microtime(true) - $start;
  echo "total  : ".($duration*1000)." ms\n";
  echo "average: ".(($duration / $numOfSamples)*1000)." ms\n";
  echo "\n";
}

function perf_array_dequeue($numOfSamples, $maxStackSize, $maxAddValues) {
  echo "array() dequeue\n";
  $stack = [];
  $start = microtime(true);
  $o = [];
  for($i=0; $i<$numOfSamples; $i++) {
    for($ii=0; $ii<$maxAddValues; $ii++) {
      $o[] = mt_rand();
    }
    $o = array_reverse($o);
    while(($v = array_pop($o)) !== null) {
      $stack[abs($i % $maxStackSize)] = $v;
    }
  }
  $duration = microtime(true) - $start;
  echo "total  : ".($duration*1000)." ms\n";
  echo "average: ".(($duration / $numOfSamples)*1000)." ms\n";
  echo "\n";
}

$numOfSamples = 100000;
$maxStackSize = 10;
$maxAddValues = 1000;

perf_SplQueue($numOfSamples, $maxStackSize, $maxAddValues);
perf_array($numOfSamples, $maxStackSize, $maxAddValues);

perf_SplQueue_dequeue($numOfSamples, $maxStackSize, $maxAddValues);
perf_array_dequeue($numOfSamples, $maxStackSize, $maxAddValues);
