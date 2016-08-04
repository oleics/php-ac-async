<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Select;

Testa::Spec(function(){

  describe('class Select ($timeoutSeconds = 0, $timeoutMicroseconds = 200000)', function() {

    it('is available under "Ac\Async\Select"', function() {
      assert(Select::class === 'Ac\Async\Select');
    });

    describe('Constants', function(){
      describe('I/O', function(){
        describe('CHUNK_SIZE');
        describe('READ_BUFFER_SIZE');
        describe('WRITE_BUFFER_SIZE');
      });
      describe('State', function(){
        describe('IDLE');
        describe('ACTIVE');
        describe('DONE');
      });
    });

    describe('Instance', function(){
      describe('Properties', function(){
      });

      describe('Methods', function(){
        describe('->select()');

        describe('Add / Remove Streams', function() {
          describe('->addReadable($stream)');
          describe('->removeReadable($stream)');
          describe('->numReadables()');
          describe('->addWritable($stream)');
          describe('->removeWritable($stream)');
          describe('->numWritables()');
          describe('->addExceptable($stream)');
          describe('->removeExceptable($stream)');
          describe('->numExceptables()');
        });

        describe('Stream State Change Callbacks', function() {
          describe('->addCallbackReadable(callable $callback, $stream = null)');
          describe('->removeCallbackReadable(callable $callback)');
          describe('->addCallbackWritable(callable $callback, $stream = null)');
          describe('->removeCallbackWritable(callable $callback)');
          describe('->addCallbackExceptable(callable $callback, $stream = null)');
          describe('->removeCallbackExceptable(callable $callback)');
        });

        describe('General State Change Callbacks', function() {
          describe('->addIdleCallback(callable $callback)');
          describe('->removeIdleCallback(callable $callback)');
          describe('->addActiveCallback(callable $callback)');
          describe('->removeActiveCallback(callable $callback)');
          describe('->addDoneCallback(callable $callback)');
          describe('->removeDoneCallback(callable $callback)');
        });
      });
    });

  });

});
