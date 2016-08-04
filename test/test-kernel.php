<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Ac\Testa\Testa;
use Ac\Async\Kernel;

Testa::add(function(){
});
//
// Testa::add(function(){
//   it('foo', function(){
//     it('bar');
//     it('baz');
//   });
// });

Testa::Spec(function(){

  describe('class Kernel($framerate = null, StreamSelect &$select = null, $defaultToFileMode = false)', function() {

    it('is available under "Ac\Async\Kernel"', function() {
      assert(Kernel::class === 'Ac\Async\Kernel');
    });

    describe('Instances', function(){
      beforeEach(function($ctx){
        $ctx->kernel = new Kernel(1/60);
      });

      afterEach(function($ctx){
        $ctx->kernel = null;
      });

      describe('$kernel->isRunning', function() {
        it('is a flag', function($ctx) {
          assert(isset($ctx->kernel->isRunning), 'isRunning-prop is defined.');
          assert(is_bool($ctx->kernel->isRunning), 'isRunning-prop is a boolean.');
        });
      });

      describe('$kernel->start(callback $frame = null)', function(){
        it('is a callable', function($ctx) {
          assert(is_callable([&$ctx->kernel, 'start']), 'start-prop is a callable.');
        });

        it('starts the kernel', function($ctx) {
          $ctx->kernel->start(function($kernel){
            if($kernel->frame == 2) {
              $kernel->stop();
            }
          });
          assert($ctx->kernel->frame === 2);
        });

        it('sets $kernel->isRunning to true', function($ctx) {
          $ctx->kernel->start(function($kernel){
            assert($kernel->isRunning);
            $kernel->stop();
          });
        });
      });


      describe('$kernel->stop()', function(){
        it('is a callable', function($ctx) {
          assert(is_callable([&$ctx->kernel, 'stop']), 'stop-prop is a callable.');
        });

        it('sets $kernel->isRunning to false', function($ctx) {
          assert($ctx->kernel->isRunning === false);
          $ctx->kernel->start(function($kernel){
            assert($kernel->isRunning === true);
            $kernel->stop();
            assert($kernel->isRunning === false);
          });
        });
      });

      describe('$kernel->step()', function(){
        it('makes stepping through frames possible', function($ctx) {
          $ctx->kernel->start(function($kernel){
            assert($kernel->frame === 1);
            $kernel->stop();
          });
          assert($ctx->kernel->frame === 1);
          $ctx->kernel->step();
          assert($ctx->kernel->frame === 2);
          $ctx->kernel->step();
          assert($ctx->kernel->frame === 3);
          $ctx->kernel->start(function($kernel){
            assert($kernel->frame === 4);
            $kernel->stop();
          });
          assert($ctx->kernel->frame === 4);
        });
        it('throws if $kernel is running', function($ctx) {
          $ctx->kernel->start(function($kernel){
            try {
              $kernel->step();
              assert(false);
            } catch(Exception $err) {
            }
            $kernel->stop();
          });
        });
      });

      describe('$kernel->frame', function(){
        it('is an integer', function($ctx) {
          assert(isset($ctx->kernel->frame));
          assert(is_int($ctx->kernel->frame));
        });
        it('is 0 on new kernel-instances', function($ctx) {
          assert($ctx->kernel->frame === 0);
        });
      });

      describe('General Behaviour', function(){
        it('first frame is always 1', function($ctx) {
          $ctx->kernel->start(function($kernel){
            assert($kernel->frame === 1);
            $kernel->stop();
          });
        });

        it('$kernel->frame is still 1 after calling $kernel->stop() in the first frame', function($ctx) {
          $ctx->kernel->start(function($kernel){
            assert($kernel->frame === 1);
            $kernel->stop();
          });
          assert($ctx->kernel->frame === 1);
          $ctx->kernel->start(function($kernel){
            assert($kernel->frame === 2);
            $kernel->stop();
          });
          assert($ctx->kernel->frame === 2);
        });

        it('tail-size stays constant', function($ctx) {
          $ctx->kernel->start(function($kernel){
            static $tailsize;
            if(!$tailsize) {
              $tailsize = count(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
            } else {
              assert($tailsize === count(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)));
            }
            if($kernel->frame == 3) {
              $kernel->stop();
            }
          });
        });

        it('keeps in-sync with the real time', function($ctx) {
          $ctx->kernel->start(function($kernel){
            static $start;
            static $sleepFrames = 3;

            if($kernel->frame == 1) {
              $start = microtime(true);
            } else if($kernel->frame == ($sleepFrames * 2)) {
              $kernel->stop();
            } else if($kernel->frame == 2) {
              $sleeptime = ($kernel->framerate*$sleepFrames)*1e6;
              usleep($sleeptime);
            } else {
              //
              $diff = microtime(true) - ($start + ($kernel->framerate * ($kernel->frame-1)));
              $limit = $kernel->framerate * max(1, ((3-$kernel->frame) + $sleepFrames));
              assert(abs($diff) < $limit, "[frame $kernel->frame] limit $limit, diff $diff");
            }
          });
        });
      });

    });



    // describe('Static', function(){
    //   describe('Properties', function(){
    //   });
    //   describe('Functions', function(){
    //   });
    // });

  });

});

// Testa::run(__FILE__);
