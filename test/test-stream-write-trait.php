<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream;
use Ac\Async\Stream\WriteTrait;

Testa::Spec(function(){

  describe('trait Stream\WriteTrait', function() {

    it('is available under "Ac\Async\Stream\WriteTrait"', function() {
      assert(trait_exists('Ac\Async\Stream\WriteTrait'));
      assert(WriteTrait::class === 'Ac\Async\Stream\WriteTrait');
    });

    describe('Static Functions', function(){
      before(function($ctx){
        $ctx->classname = createClassWithTrait('Ac\Async\Stream\WriteTrait');
      });

      after(function($ctx){
        unset($ctx->classname);
      });

      describe('::write($stream)', function($ctx) {
        it('returns a new closure $write($data, callable $callback = null)', function($ctx, callable $done) {

          $testStreamWrite = function($classname, $stream, array $dataToWrite, callable $done) {
            for($i=0; $i<mt_rand(1, 10); $i++) {
              $dataToWrite[] = ''.mt_rand().' '.str_repeat('-', 1024*2*$i);
            }
            $dataToWritePending = count($dataToWrite);
            $numDataWritten = 0;
            $nonBlocking = false;

            $testStreamWriteFunction = function(&$write, $data) use(&$stream, &$done, &$dataToWritePending, &$numDataWritten, &$nonBlocking){
              $check = function() use(&$stream, &$done, &$data, &$dataToWritePending){
                rewind($stream);
                assert($data === fread($stream, strlen($data)));
                rewind($stream);
                ftruncate($stream, 0);
                if(--$dataToWritePending === 0) {
                  fclose($stream);
                  async($done, []);
                }
              };
              if($numDataWritten) {
                assert($nonBlocking);
              }
              $write($data, $check);
              ++$numDataWritten;
            };

            $write = call_user_func([$classname, 'write'], $stream);
            foreach($dataToWrite as $data) {
              $testStreamWriteFunction($write, $data);
              $nonBlocking = true;
            }
          };


          $testStreamWriteOnStreams = function($classname, array $streams, array $dataToWrite, callable $done) use(&$testStreamWrite) {
            $streamsPending = count($streams);
            $nonBlocking = false;

            $check = function() use(&$done, &$streamsPending, &$nonBlocking) {
              assert($nonBlocking);
              if(--$streamsPending === 0) {
                async($done, []);
              }
            };

            foreach($streams as $stream) {
              async($testStreamWrite, [$classname, $stream, $dataToWrite, $check]);
              // $testStreamWrite($classname, $stream, $dataToWrite, $check);
              $nonBlocking = true;
            }
          };

          $streams = [];
          for($i=0; $i<10; $i++) {
            $streams[] = Stream::openDuplex();
          }
          $dataToWrite = [];
          for($i=0; $i<5; $i++) {
            $dataToWrite[] = ''.mt_rand().' '.str_repeat('-', 1024*2*$i);
          }
          $testStreamWriteOnStreams($ctx->classname, $streams, $dataToWrite, $done);


        });
      });

    });
  });

});
