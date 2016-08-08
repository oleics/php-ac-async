<?php

async(function() {
  echo "enqueue 10\n";
}, null, 10);

async(function() {
  echo "enqueue 1\n";
  async(function() {
    echo "enqueue 100.a\n";
  }, null, 100);
  async(function() {
    echo "enqueue 100.b\n";
  }, null, 100);
  async(function() {
    echo "enqueue 10.a\n";
  }, null, 10);
  async(function() {
    echo "enqueue 10.b\n";
  }, null, 10);
}, null, 2);

async_schedule(mkFunc('schedule 10'), 10);
async_scheduleEach(mkFunc('scheduleEach 100'), 100);
async_setInterval(mkFunc('setInterval 1'), 1);
async_setTimeout(mkFunc('setTimeout 3.a'), 3);
async_setTimeout(mkFunc('setTimeout 3.b'), 3);

echo "Running...\n"; // will be printed first

function mkFunc($msg) {
  return function($engine) use(&$msg) {
    // async_log("[$engine->frame] $msg");
    echo("[$engine->frame] $msg\n");
  };
}
