<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Promise;

Testa::Spec(function(){

  describe('class Promise ($stream = STDOUT)', function() {

    before('enable promise non-blocking-mode', function($ctx){
      if($ctx->wasBlockingMode = ! Promise::isNonBlockingMode()) {
        Promise::enableNonBlockingMode();
      }
    });

    after('restore promise blocking-mode', function($ctx){
      if($ctx->wasBlockingMode) {
        Promise::disableNonBlockingMode();
      }
    });

    it('is available under "Ac\Async\Promise"', function() {
      assert(Promise::class === 'Ac\Async\Promise');
    });

    describe('Instances', function(){
      it('implements Promises, Part I', function(callable $done) {
        // $promise = Promise::resolve(1);
        $promise = new Promise(function($resolve, $reject){
          async_setTimeout(function() use(&$resolve) {
            $resolve(1);
          }, 0.01);
        });

        $promise
          ->then(function($d){
            assert($d === 1);
            return $d + 1;
          })
          ->then(function($d){
            assert($d === 2);
            return $d + 1;
          })
          ->then(function($d){
            assert($d === 3);
            return $d + 1;
          })
          ->then(function($d){
            assert($d === 4);
          })
          ->then(function($d){
            assert($d === 4);
            $promise = new Promise(function($resolve, $reject){
              async_setTimeout(function() use(&$resolve) {
                $resolve(5);
              }, 0.01);
            });
            return $promise->then(function($d){
              assert($d === 5);
              $promise = new Promise(function($resolve, $reject){
                async_setTimeout(function() use(&$resolve) {
                  $resolve(6);
                }, 0.01);
              });
              return $promise;
            });
          })
          ->then(function($d){
            assert($d === 6);
            return Promise::nullAsResult();
          })
          ->then(function($d){
            assert($d === null);
            throw new Exception('oops!');
          })
          ->catchReject(function(Exception $d){
            assert($d->getMessage() === 'oops!');
            assert($d instanceof Exception);
          })
          ->then(function($d){
            assert($d === null);
            return 7;
          })
          ->catchReject(function(){
            assert(false, 'Will never be called.');
          })
          ->then(function($d){
            assert($d === 7);
            return $d + 1;
          })
          ->then(function($d) use(&$done) {
            assert($d === 8);
            $done();
            throw new Exception('oops, i did it again!');
          })
          ->then(function($d){
            assert(false, 'Will never be called.');
          })
        ;
      });

      it('implements Promises, Part II', function(callable $done) {
        $called = 0;
        $str = '';

        $doWork = function() use(&$called, &$str) {
          ++$called;
          $str .= 'W';
          return Promise::resolve();
        };
        $doError = function() use(&$called, &$str) {
          ++$called;
          $str .= 'E';
          throw new Exception('oops!');
        };
        $errorHandler = function($err) use(&$called, &$str) {
          ++$called;
          $str .= 'H';
        };
        $verify = function($be) use(&$called, &$str, &$done) {
          ++$called;
          return function() use(&$be, &$str, &$done) {
            assert($be === $str);
            $done();
          };
        };

        $doWork()
          ->then($doWork)
          ->then($doError)
          ->then($doWork)
          ->then($doWork, $errorHandler)
          ->then($verify('WWEH'))
          ->catchReject($done)
        ;

        assert(5 === $called, "called 5 === $called");
      });

      describe('->__construct(callable $executor)', function(){
        it('constructs a new Promise', function(){
          new Promise(function(){});
        });
      });

      describe('->then(callable|Promise|mixed $onFulfilled = null, callable $onRejected = null)', function(){
        it('appends actions to run after a promise was fulfilled or rejected', function(){
          if($isNonBlockingMode = Promise::isNonBlockingMode()) {
            Promise::disableNonBlockingMode();
          }

          $called = 0;

          $resolveMe;
          $promise = new Promise(function($resolve) use(&$resolveMe){
            $resolveMe = $resolve;
          });
          $promise->then(function($d) use(&$called){
            $called++;
            assert(1 === $d);
          });

          assert(0 === $called, "called 0 === $called");
          $resolveMe(1);
          assert(1 === $called, "called 1 === $called");
          try {
            $resolveMe(1);
            assert(false);
          } catch(Exception $err) {
            assert($err instanceof Exception);
          }

          $promise->then(function($d) use(&$called){
            $called++;
            assert(1 === $d);
          });
          assert(2 === $called, "called 2 === $called");

          $promise->then(function($d) use(&$called){
            $called++;
            assert(1 === $d);
            throw new Exception('ooops!');
          }, function($d) use(&$called){
            $called++;
            assert('ooops!' === $d->getMessage());
          });
          assert(4 === $called, "called 4 === $called");

          if($isNonBlockingMode) {
            Promise::enableNonBlockingMode();
          }
        });

        it('returns a Promise', function(){
          $promise = new Promise(function($resolve){ $resolve(1); });
          assert($promise->then(function(){ }) instanceof Promise);
        });
      });

      describe('->catchReject(callable $onRejected = null)', function(){
        it('appends an action to run after a promise was rejected', function(){
          $called = 0;

          $resolveMe;
          $rejectMe;
          $promise = new Promise(function($resolve, $reject) use(&$resolveMe, &$rejectMe){
            $resolveMe = $resolve;
            $rejectMe = $reject;
          });
          $promise->then(function() use(&$called){
            $called++;
            assert(false);
          })->catchReject(function($reason) use(&$called){
            $called++;
            assert('reason' === $reason);
          })->then(function($d) use(&$called){
            $called++;
            assert(null === $d);
          });
          assert(0 === $called, "called 0 === $called");
          $rejectMe('reason');
          assert(2 === $called, "called 2 === $called");
        });
      });
    });

    describe('Static', function(){
      describe('Methods', function(){
        describe('::isThenable($value)', function(){
          it('returns TRUE for `thenable` values, otherwise FALSE', function(){
            assert(false === Promise::isThenable(null));
            assert(false === Promise::isThenable(1));
            assert(false === Promise::isThenable('two'));
            assert(true === Promise::isThenable(Promise::resolve(321)));
          });
        });

        describe('::runThenable($value, callable $onFulfilled = null, callable $onRejected = null)', function() {
          it('executes `thenable` values', function(callable $done) {
            $thenables = [
              Promise::resolve(123),
              [ 'then' => function(callable $onFulfilled = null, callable $onRejected = null){
                $onFulfilled(456);
              } ],
              function(callable $onFulfilled = null, callable $onRejected = null){
                $onFulfilled(789);
              },
              function(callable $onFulfilled = null, callable $onRejected = null){
                $onRejected(101112);
              }
            ];

            $resultsExpected = [
              [123],
              [456],
              [789],
              [null,101112],
            ];

            $calledExpected = count($thenables);
            $calledFulfilledExpected = count(array_filter($resultsExpected, function($value){ return count($value) === 1; }));
            $calledRejectedExpected = count(array_filter($resultsExpected, function($value){ return count($value) === 2; }));

            $pending = count($thenables);
            $called = 0;
            $calledFulfilled = 0;
            $calledRejected = 0;

            $results = [];

            $check = function($key, $value = null, $reason = null) use(
                                  &$results, &$resultsExpected,
                                  &$called, &$calledExpected,
                                  &$calledFulfilled, &$calledFulfilledExpected,
                                  &$calledRejected, &$calledRejectedExpected,
                                  &$pending, &$done
                                )
            {
              $results[$key] = [$value, $reason];
              if(--$pending <= 0) {
                assert($calledExpected === $called);
                assert($calledFulfilledExpected === $calledFulfilled);
                assert($calledRejectedExpected === $calledRejected);
                assert(count($resultsExpected) === count($results));
                foreach($resultsExpected as $key => $value) {
                  assert($value[0] === $results[$key][0]);
                  assert(@$value[1] === @$results[$key][1]);
                }
                foreach($results as $key => $value) {
                  assert($resultsExpected[$key][0] === $value[0]);
                  assert(@$resultsExpected[$key][1] === @$value[1]);
                }
                return $done();
              }
            };

            foreach($thenables as $key => $value) {
              Promise::runThenable(
                $value,
                function($value = null) use(&$called, &$calledFulfilled, &$check, $key) {
                  ++$called;
                  ++$calledFulfilled;
                  $check($key, $value);
                },
                function($reason) use(&$called, &$calledRejected, &$check, $key) {
                  ++$called;
                  ++$calledRejected;
                  $check($key, null, $reason);
                }
              );
            }
          });
        });

        describe('::resolve(mixed $value)', function(){
          it('returns a new promise resolved to $value', function(){
            $called = 0;
            $promise = Promise::resolve(321);
            $promise->then(function($d) use(&$called){
              $called++;
              assert(321 === $d);
            });
            assert(1 === $called, "called 1 === $called");
          });
        });

        describe('::reject(mixed $reason)', function(){
          it('returns a new promise rejected with reason $reason', function(){
            $called = 0;
            $promise = Promise::reject(321);
            $promise->catchReject(function($d) use(&$called){
              $called++;
              assert(321 === $d);
            });
            assert(1 === $called, "called 1 === $called");
          });
        });

        describe('::all(array $iterable)', function(){
          it('resolves an array of promises (parallel)', function(callable $done){
            Promise::all([
              1, null,
              Promise::resolve(3),
              new Promise(function($resolve, $reject){
                async_setTimeout(function() use(&$resolve) {
                  $resolve(4);
                }, 0.02);
              }),
              function($resolve, $reject){
                async_setTimeout(function() use(&$resolve) {
                  $resolve(5);
                }, 0.01);
              }
            ])->then(function($d) use(&$done){
              assert($d[0] === 1);
              assert($d[1] === null);
              assert($d[2] === 3);
              assert($d[3] === 4);
              assert($d[4] === 5);
              assert(count($d) === 5);
              assert(array_keys($d) === range(0, 4), "Keys are on-order.");
              $done();
            })->catchReject($done);
          });

          it('rejects on first rejected promise');
        });

        describe('::parallel(array $iterable, $maxParallel = PHP_INT_MAX)', function(){
          it('resolves an array of promises (parallel)', function(callable $done){
            Promise::parallel([
              1, null,
              Promise::resolve(3),
              new Promise(function($resolve, $reject){
                async_setTimeout(function() use(&$resolve) {
                  $resolve(4);
                }, 0.03);
              }),
              [ 'then' => function(callable $onFulfilled = null, callable $onRejected = null){
                async_setTimeout(function() use(&$onFulfilled) {
                  $onFulfilled(5);
                }, 0.02);
              } ],
              function(callable $onFulfilled = null, callable $onRejected = null){
                async_setTimeout(function() use(&$onFulfilled) {
                  $onFulfilled(6);
                }, 0.01);
              },
            ], 1)->then(function($d) use(&$done){
              assert(   1 === $d[0][0], "result    1 === ".$d[0][0]."");
              assert(null === $d[1][0], "result null === ".$d[1][0]."");
              assert(   3 === $d[2][0], "result    2 === ".$d[2][0]."");
              assert(   4 === $d[3][0], "result    3 === ".$d[3][0]."");
              assert(   5 === $d[4][0], "result    4 === ".$d[4][0]."");
              assert(   6 === $d[5][0], "result    5 === ".$d[5][0]."");
              assert(   6 === count($d));
              assert(range(0, 5) === array_keys($d), "Keys are on-order.");
              $done();
            }, $done);
          });
          it('limits the number of parallel executions');
          it('always resolves to an array of [value,reason]-pairs');
        });

        describe('::race(array $arr)');
      });
    });

  });

});
