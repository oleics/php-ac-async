<?php

$bytes = isset($_REQUEST['bytes']) ? (int) $_REQUEST['bytes'] : 100;
$usleep = isset($_REQUEST['usleep']) ? (int) $_REQUEST['usleep'] : 500;

for($i=0; $i<$bytes; $i++) {
  echo "d";
  if($i % 10 === 0) {
    flush();
    usleep($usleep);
  }
}
