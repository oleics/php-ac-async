<?php

use \SplFixedArray;

$numOfSamples = 10000000;
$maxStackSize = 100;

echo "SplFixedArray::fromArray(...)\n";
$stack = [];
$start = microtime(true);
for($i=0; $i<$numOfSamples; $i++) {
  $stack[abs($i % $maxStackSize)] = SplFixedArray::fromArray([$i, $i+1]);
}
$duration = microtime(true) - $start;
echo "total  : ".($duration/1000)." ms\n";
echo "average: ".(($duration / $numOfSamples)/1000)." ms\n";
echo "\n";

echo "SplFixedArray(2)\n";
$stack = [];
$start = microtime(true);
for($i=0; $i<$numOfSamples; $i++) {
  $d = new SplFixedArray(2);
  $d[0] = $i;
  $d[1] = $i+1;
  $stack[abs($i % $maxStackSize)] = $i+1;
}
$duration = microtime(true) - $start;
echo "total  : ".($duration/1000)." ms\n";
echo "average: ".(($duration / $numOfSamples)/1000)." ms\n";
echo "\n";
