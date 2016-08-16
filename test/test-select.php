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
        describe('STATE_IDLE');
        describe('STATE_ACTIVE');
        describe('STATE_DONE');
      });
    });

    describe('Instance', function(){
      describe('Properties', function(){
      });

      describe('Methods', function(){
        describe('->__construct($timeoutSeconds = 0.2)');
        describe('->setTimeout($timeoutSeconds)');
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

      });

      describe('Events', function(){
        describe('Add / Remove Streams', function(){
          describe('add-readable $stream');
          describe('add-writable $stream');
          describe('add-exceptable $stream');
          describe('remove-readable $stream');
          describe('remove-writable $stream');
          describe('remove-exceptable $stream');
        });

        describe('Stream State Change', function(){
          describe('readable $stream');
          describe('readable-[streamid] $stream');
          describe('writable $stream');
          describe('writable-[streamid] $stream');
          describe('exceptable $stream');
          describe('exceptable-[streamid] $stream');
          describe('invalid $stream');
          describe('invalid-[streamid] $stream');
        });

        describe('General State Change', function(){
          describe('idle');
          describe('active');
          describe('done');
        });
      });
    });

    describe('Static', function(){
      describe('Methods', function(){
        describe('->streamId($stream)');
      });
    });
  });

});
