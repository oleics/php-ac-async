<?php

use Ac\Async\Stream;

$write = Stream::write(Stream::openReadable());

$write('foo', function(){
  echo "'foo' written\n";
});

$write('bar', function(){
  echo "'bar' written\n";
});

echo "Writing...\n"; // will be printed first
