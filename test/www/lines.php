<?php

$lines = isset($_REQUEST['lines']) ? (int) $_REQUEST['lines'] : 100;
$usleep = isset($_REQUEST['usleep']) ? (int) $_REQUEST['usleep'] : 500;

for($i=0; $i<$lines; $i++) {
  if($i>0) echo "\n";
  echo "line $i";
  if($i % 10 === 0) {
    flush();
    usleep($usleep);
  }
}
