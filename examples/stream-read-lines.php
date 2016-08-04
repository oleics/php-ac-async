<?php

use Ac\Async\Stream;

$readable = Stream::openReadable('http://google.com');

$unbind;
$unbind = Stream::readLines($readable, function($line) use(&$unbind) {
  static $lines = 0;
  static $linesMax = 5;
  if($line === null) {
    echo "No more lines, closing stream...\n";
    return;
  }
  ++$lines;
  echo "Got line $lines.\n";
  if($lines === $linesMax) {
    $unbind();
  }
});

echo "Reading lines...\n"; // will be printed first
