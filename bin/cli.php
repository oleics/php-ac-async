<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ac\Async\Async;

function usage($msg = null) {
  if($msg) {
    echo "$msg\n";
  }
  echo "Usage: async.phar filename\n";
  die();
}

if(empty($argv[1])) usage('Missing argument "filename".');
$filename = $argv[1];

Async::wrap($filename);
