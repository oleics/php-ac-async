<?php

use Ac\Async\Stream;

$readable = Stream::openReadable('http://google.com');

$unbind = Stream::read($readable, function($data) {
  if($data === null) {
    // no more data, $stream will be closed.
    echo "No more data, closing...\n";
    return;
  }
  echo 'Got '.strlen($data)." bytes.\n";
});

echo "Reading data...\n"; // will be printed first
