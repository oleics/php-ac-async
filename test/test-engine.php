<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Ac\Async\Async;
use Ac\Testa\Testa;
use Ac\Async\Kernel;
use Ac\Async\Engine;

Testa::Spec(function(){

  describe('class Engine($framerate = null, Kernel &$kernel = null)', function() {

    it('is available under "Ac\Async\Engine"', function() {
      assert(Engine::class === 'Ac\Async\Engine');
    });

    function makeEngineInstance($framerate) {
      $kernel = Async::getKernel();
      $engine = new Engine($framerate, $kernel);
      return $engine;
    }

    function test_Engine_enqueue(Engine $engine) {
      describe('$engine->enqueue(callable $fn, array $args = null, $priority = 0)', function() use(&$engine) {

        beforeEach(function($ctx) use(&$engine){
          $ctx->engine = $engine;
          $ctx->kernel = $ctx->engine->getKernel();
        });

        afterEach(function($ctx){
          $ctx->engine = null;
          $ctx->kernel = null;
        });

        // TODO: Refine this test.
        // TODO: Order of funcs.
        it('enqueue', function($ctx, callable $done) {
          $called = 0;

          $calls = [
            (object) [
              'priority'      => 0,
              'msg'           => 'prio 0',
              'expectedFrame' => 1,
              'sleep'         => $ctx->kernel->framerate / 2
            ],
            (object) [
              'priority'      => 100,
              'msg'           => 'prio 100.a',
              'expectedFrame' => 2,
              'sleep'         => $ctx->engine->framerate
            ],
            (object) [
              'priority'      => 10,
              'msg'           => 'prio 10.a',
              'expectedFrame' => 1,
              'sleep'         => $ctx->engine->framerate / 2
            ],
            (object) [
              'priority'      => 10,
              'msg'           => 'prio 10.b',
              'expectedFrame' => 1,
              'sleep'         => $ctx->engine->framerate / 2
            ],
            (object) [
              'priority'      => 100,
              'msg'           => 'prio 100.b',
              'expectedFrame' => 2,
              'sleep'         => $ctx->engine->framerate
            ],
            (object) [
              'priority'      => 100,
              'msg'           => 'prio 100.c',
              'expectedFrame' => 2,
              'sleep'         => $ctx->engine->framerate
            ],
            (object) [
              'priority'      => 1000,
              'msg'           => 'prio 1000',
              'expectedFrame' => 3,
              'sleep'         => $ctx->engine->framerate
            ],
          ];
          $expectedOrder = [];
          $stopAtFrame = 0;

          $test_enqueue = function(&$engine, $msg, $priority, $expectedFrame = null, $sleep = null) use(&$called, &$expectedOrder) {
            $expectedOrder[$priority][] = $msg;
            $expectedRndArg = rand();
            $expectedFrame += $engine->frame;
            $engine->enqueue(function(Engine $engine, $rndArg) use(&$sleep, &$msg, &$called, &$expectedRndArg, &$expectedFrame, &$expectedOrder, &$priority) {
              ++$called;
              // if(isset($msg)) $engine->log($msg);
              if(isset($sleep)) usleep($sleep * 1e6);
              if(isset($expectedFrame)) assert($expectedFrame === $engine->frame, '['.$msg.'] enqueue expectedFrame '.$expectedFrame.' === '.$engine->frame.'');
              assert(array_shift($expectedOrder[$priority]) === $msg, "enqueue order");
              assert($expectedRndArg === $rndArg, "enqueue expectedRndArg $expectedRndArg === $rndArg");
            }, [$engine, $expectedRndArg], $priority);
          };

          foreach($calls as $c) {
            $test_enqueue($ctx->engine, $c->msg, $c->priority, $c->expectedFrame, $c->sleep);
            $stopAtFrame = max($c->expectedFrame, $stopAtFrame);
          }
          $stopAtFrame += $ctx->engine->frame + 1;

          $ctx->engine->start(function(Engine &$engine) use(&$stopAtFrame, &$called, &$done) {
            if($engine->frame >= $stopAtFrame) {
              assert($engine->stop());
              assert(7 === $called, 'enqueue called 7 === '.$called.'');
              $done();
            }
          });
        });

      });
    }

    function test_Engine_schedule(Engine $engine) {
      describe('$engine->schedule(callable $fn, array $args = null, $forFrame = 0)', function() use(&$engine) {

        beforeEach(function($ctx) use(&$engine){
          $ctx->engine = $engine;
          $ctx->kernel = $ctx->engine->getKernel();
        });

        afterEach(function($ctx){
          $ctx->engine = null;
          $ctx->kernel = null;
        });

        $test_schedule = function($frame = 0){
          it('schedule '.$frame.'', function($ctx, callable $done) use(&$frame) {
            it_timeout((($frame ? $frame : 1) * $ctx->engine->framerate) + $ctx->engine->framerate, true);
            $called = 0;
            $expectedFrame = ($frame ? $frame : 1) + $ctx->engine->frame;
            $expectedRndArg = rand();
            $ctx->engine->schedule(function(Engine $engine, $rndArg) use(&$frame, &$expectedFrame, &$expectedRndArg, &$called){
              ++$called;
              assert($expectedFrame === $engine->frame, "schedule expectedFrame $expectedFrame === $engine->frame");
              assert($expectedRndArg === $rndArg, "schedule expectedRndArg $expectedRndArg === $rndArg");
            }, [$ctx->engine, $expectedRndArg], $frame);

            $ctx->engine->start(function(Engine &$engine) use(&$frame, &$expectedFrame, &$called, &$done) {
              if($engine->frame > $expectedFrame) {
                assert($engine->stop());
                assert(1 === $called, 'schedule called 1 === '.$called.'');
                $done();
              }
            });
          });
        };

        $test_schedule();
        $test_schedule(0);
        $test_schedule(1);
        $test_schedule(2);
        $test_schedule(3);
        $test_schedule(4);
        $test_schedule(5);
        $test_schedule(6);

      });
    }

    function test_Engine_scheduleEach(Engine $engine) {
      describe('$engine->scheduleEach(callable $fn, $eachFrame = 0)', function() use(&$engine) {

        beforeEach(function($ctx) use(&$engine){
          $ctx->engine = $engine;
          $ctx->kernel = $ctx->engine->getKernel();
        });

        afterEach(function($ctx){
          $ctx->engine = null;
          $ctx->kernel = null;
        });

        $test_scheduleEach = function($frame = 0, $calledExpected = 3) {
          it('scheduleEach '.$frame.'', function($ctx, callable $done) use(&$frame, &$calledExpected) {
            it_timeout((($frame ? $frame : 1) * $ctx->engine->framerate) * $calledExpected + $ctx->engine->framerate, true);

            $called = 0;
            $startFrame = $prevFrame = $ctx->engine->frame;
            $stopAtFrame = $startFrame + (($frame ? $frame : 1) * $calledExpected);

            $expectedFrames = ($frame ? $frame : 1);
            $expectedTotalFrames = ($frame ? $frame : 1) * $calledExpected;
            $expectedRndArg = rand();

            $fn = function(Engine $engine, $rndArg) use(&$expectedFrames, &$prevFrame, &$expectedRndArg, &$called) {
              ++$called;
              $frames = $engine->frame - $prevFrame;
              $prevFrame = $engine->frame;
              assert($expectedFrames === $frames, "scheduleEach frames $expectedFrames === $frames");
              assert($expectedRndArg === $rndArg, "scheduleEach expectedRndArg $expectedRndArg === $rndArg");
            };

            $ctx->engine->scheduleEach($fn, [$ctx->engine, $expectedRndArg], $frame);

            $ctx->engine->start(function(Engine &$engine) use(&$fn, &$stopAtFrame, &$calledExpected, &$called, &$done) {
              if($engine->frame >= $stopAtFrame) {
                assert($engine->stop());
                assert($engine->removeFromSchedules($fn));
                assert($calledExpected === $called, 'scheduleEach called '.$calledExpected.' === '.$called.'');
                $done();
              }
            });

          });
        };

        $test_scheduleEach();
        for($i=0; $i<10; $i++) {
          $test_scheduleEach($i);
        }

      });
    }

    function test_Engine_setTimeout(Engine $engine) {
      describe('$engine->setTimeout(callable $fn, $seconds = 0.0)', function() use(&$engine) {

        beforeEach(function($ctx) use(&$engine){
          $ctx->engine = $engine;
          $ctx->kernel = $ctx->engine->getKernel();
        });

        afterEach(function($ctx){
          $ctx->engine = null;
          $ctx->kernel = null;
        });

        $test_setTimeout = function($timeout) {
          it('timeout '.$timeout.'', function($ctx, callable $done) use(&$timeout) {
            it_timeout($timeout * 1.5, true);

            $called = 0;
            $startFrame = $ctx->engine->frame;
            $stopAtFrame = $startFrame + ceil($timeout / $ctx->engine->framerate);
            $timeoutStart = microtime(true);
            $expectedFrame = (int)($startFrame + ceil($timeout / $ctx->engine->framerate));
            $expectedRndArg = rand();

            $ctx->engine->setTimeout(function(Engine $engine, $rndArg) use(&$timeout, &$called, &$timeoutStart, &$expectedFrame, &$expectedRndArg) {
              ++$called;
              $timeoutElapsed = microtime(true) - $timeoutStart;
              $timeoutExpected = $timeout - $engine->framerate - ($engine->framerate*0.4);
              assert($expectedFrame === $engine->frame, "setTimeout expectedFrame $expectedFrame === $engine->frame (timeout $timeout)");
              assert($timeoutExpected <= $timeoutElapsed, "setTimeout timeoutExpected $timeoutExpected <= $timeoutElapsed (timeout $timeout)");
              assert($expectedRndArg === $rndArg, "setTimeout expectedRndArg $expectedRndArg === $rndArg");
            }, [$ctx->engine, $expectedRndArg], $timeout);

            $ctx->engine->start(function(Engine &$engine) use(&$stopAtFrame, &$called, &$done) {
              if($engine->frame >= $stopAtFrame) {
                assert($engine->stop());
                assert(1 === $called, "timeout called 1 === $called");
                $done();
              }
            });

          });
        };

        $test_setTimeout(1/33);
        $test_setTimeout(1/30);
        $test_setTimeout(1/27);
        $test_setTimeout(1/24);
        $test_setTimeout(1/21);
        $test_setTimeout(1/18);
        $test_setTimeout(1/15);
        $test_setTimeout(1/12);
        $test_setTimeout(1/9);
        $test_setTimeout(1/6);
        $test_setTimeout(1/5);
        $test_setTimeout(1/4);
        $test_setTimeout(1/3);
        $test_setTimeout(1/2);
        $test_setTimeout(1);
        $test_setTimeout(1.5);

      });
    }

    function test_Engine_setInterval(Engine $engine) {
      describe('$engine->setInterval(callable $fn, $seconds = 0.0)', function() use(&$engine) {

        beforeEach(function($ctx) use(&$engine){
          $ctx->engine = $engine;
          $ctx->kernel = $ctx->engine->getKernel();
        });

        afterEach(function($ctx){
          $ctx->engine = null;
          $ctx->kernel = null;
        });

        $test_setInterval = function($interval, $calledExpected = 10) {
          it('interval '.$interval.'', function($ctx, callable $done) use(&$interval, &$calledExpected) {
            it_timeout(($interval * ($calledExpected + 0.5)), true);

            $called = 0;

            $startFrame = $prevFrame = $ctx->engine->frame;
            $expectedFrames = (int) ceil($interval / $ctx->engine->framerate);
            $expectedTotalFrames = (int) ceil((max($interval, $ctx->engine->framerate) * $calledExpected) / $ctx->engine->framerate);
            $expectedTotalFrames = $expectedFrames  * $calledExpected;
            $expectedRndArg = rand();

            $fn = function(Engine $engine, $rndArg) use(&$called, &$prevFrame, &$expectedFrames, &$expectedRndArg) {
              ++$called;
              $frames = $engine->frame - $prevFrame;
              $prevFrame = $engine->frame;
              if($called > 1) {
                assert($expectedFrames === $frames, "setInterval frames $expectedFrames === $frames");
              }
              assert($expectedRndArg === $rndArg, "setInterval expectedRndArg $expectedRndArg === $rndArg");
            };

            $intervalId = $ctx->engine->setInterval($fn, [$ctx->engine, $expectedRndArg], $interval);

            $ctx->engine->start(function(Engine &$engine) use(&$fn, &$calledExpected, &$called, &$startFrame, &$expectedTotalFrames, &$done) {
              if($calledExpected === $called) {
                assert($engine->stop());
                assert($engine->removeFromSchedules($fn));

                $frames = $engine->frame - $startFrame;
                assert($expectedTotalFrames === $frames, "setInterval frames total $expectedTotalFrames === $frames");
                $done();
              }
            });

          });
        };

        $test_setInterval(1/33, 17);
        $test_setInterval(1/30, 16);
        $test_setInterval(1/27, 15);
        $test_setInterval(1/24, 14);
        $test_setInterval(1/21, 13);
        $test_setInterval(1/18, 12);
        $test_setInterval(1/15, 11);
        $test_setInterval(1/12, 10);
        $test_setInterval(1/9,   9);
        $test_setInterval(1/6,   8);
        $test_setInterval(1/5,   7);
        $test_setInterval(1/4,   6);
        $test_setInterval(1/3,   5);
        $test_setInterval(1/2,   4);
        $test_setInterval(1,     3);
        $test_setInterval(1.5,   3);

      });
    }

    function test_EngineInstance(Engine $engine) {

      describe('Instance (framerate '.$engine->framerate.')', function() use(&$engine) {

        beforeEach(function($ctx) use(&$engine){
          $ctx->engine = $engine;
          $ctx->kernel = $ctx->engine->getKernel();
        });

        afterEach(function($ctx){
          $ctx->engine = null;
          $ctx->kernel = null;
        });

        describe('$engine->getKernel()', function() {
          it('returns a Kernle-instance', function($ctx){
            assert($ctx->engine->getKernel() instanceof \Ac\Async\Kernel);
          });
        });

        describe('$engine->setKernel(Kernel &$kernel)');
        describe('$engine->changeFramerate($framerate)');
        describe('$engine->start(callable $onFrame = null)');
        describe('$engine->step()');
        describe('$engine->stop()');

        test_Engine_enqueue($engine);
        test_Engine_schedule($engine);
        test_Engine_scheduleEach($engine);
        test_Engine_setTimeout($engine);
        test_Engine_setInterval($engine);

      });
    }

    test_EngineInstance(makeEngineInstance( 1/3));
    test_EngineInstance(makeEngineInstance(1/30));
    test_EngineInstance(makeEngineInstance(1/60));

  });

});
