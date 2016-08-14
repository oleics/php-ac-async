<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\EventEmitterTrait;

Testa::Spec(function(){

  describe('trait EventEmitterTrait', function() {
    before(function($ctx){
      $ctx->classname = createClassWithTrait('Ac\Async\EventEmitterTrait');
    });

    after(function($ctx){
      unset($ctx->classname);
    });

    it('is available under "Ac\Async\EventEmitterTrait"', function() {
      assert(EventEmitterTrait::class === 'Ac\Async\EventEmitterTrait');
    });

    describe('Instance', function() {

      describe('Properties', function() {
        describe('Static', function() {
          describe('$defaultMaxListeners = 10');
        });
      });

      describe('Methods', function() {
        describe('->emit($event /*, $arg1, ...,  $argX*/)', function() {
          it('is a method', function($ctx) {
            $emitter = new $ctx->classname();
            assert(method_exists($emitter, 'emit'));
          });

          it('returns emitter, so calls can be chained', function($ctx) {
            $emitter = new $ctx->classname();
            assert($emitter === $emitter->emit('foo', 'bar'));
          });

          it('emits events', function($ctx) {
            $emitter = new $ctx->classname();
            $emitter->emit('foo', 'bar');
          });
        });

        describe('->on|addListener($event, callable $listener)', function() {
          it('is a method', function($ctx) {
            $emitter = new $ctx->classname();
            assert(method_exists($emitter, 'addListener'));
            assert(method_exists($emitter, 'on'));
          });

          it('returns emitter, so calls can be chained', function($ctx) {
            $emitter = new $ctx->classname();
            assert($emitter === $emitter->addListener('foo', function(){}));
            assert($emitter === $emitter->on('foo', function(){}));
          });

          it('listens to events', function($ctx) {
            $called = 0;
            $emitter = new $ctx->classname();
            $emitter->addListener('foo', function($bar, $baz) use(&$called) {
              $called++;
              assert('bar' === $bar);
              assert('baz' === $baz);
            });
            $emitter->on('foo', function($bar, $baz) use(&$called) {
              $called++;
              assert('bar' === $bar);
              assert('baz' === $baz);
            });
            $emitter->emit('foo', 'bar', 'baz');
            assert(2 === $called);
          });
        });

        describe('->once($event, callable $listener)', function() {
          it('is a method', function($ctx) {
            $emitter = new $ctx->classname();
            assert(method_exists($emitter, 'once'));
          });

          it('returns emitter, so calls can be chained', function($ctx) {
            $emitter = new $ctx->classname();
            assert($emitter === $emitter->once('foo', function(){}));
          });

          it('listens to events once', function($ctx) {
            $called = 0;
            $emitter = new $ctx->classname();
            $emitter->once('foo', function($bar, $baz) use(&$called) {
              $called++;
              assert('bar' === $bar);
              assert('baz' === $baz);
            });
            $emitter->emit('foo', 'bar', 'baz');
            $emitter->emit('foo', 'bar', 'baz');
            assert(1 === $called);
          });
        });

        describe('->removeListener($event, callable $listener)', function() {
          it('is a method', function($ctx) {
            $emitter = new $ctx->classname();
            assert(method_exists($emitter, 'removeListener'));
          });

          it('returns emitter, so calls can be chained', function($ctx) {
            $emitter = new $ctx->classname();
            assert($emitter === $emitter->removeListener('foo', function(){}));
          });

          it('removes a listener from an event', function($ctx) {
            $called = 0;
            $emitter = new $ctx->classname();
            $listener = function($bar, $baz) use(&$called) {
              $called++;
              assert('bar' === $bar);
              assert('baz' === $baz);
            };
            $emitter->on('foo', $listener);
            $emitter->emit('foo', 'bar', 'baz');
            $emitter->removeListener('foo', $listener);
            $emitter->emit('foo', 'bar', 'baz');
            assert(1 === $called);
          });
        });
        describe('->removeAllListeners($event = null)', function() {
          it('is a method', function($ctx) {
            $emitter = new $ctx->classname();
            assert(method_exists($emitter, 'removeAllListeners'));
          });

          it('returns emitter, so calls can be chained', function($ctx) {
            $emitter = new $ctx->classname();
            assert($emitter === $emitter->removeAllListeners());
          });

          it('removes all listener from an event or all events', function($ctx) {
            $called = 0;
            $emitter = new $ctx->classname();

            $emitter->on('foo', function($buz) use(&$called) {
              $called++;
              assert('buz' === $buz);
            });
            $emitter->on('bar', function($buz) use(&$called) {
              $called++;
              assert('buz' === $buz);
            });
            $emitter->on('baz', function($buz) use(&$called) {
              $called++;
              assert('buz' === $buz);
            });

            $emitter->emit('foo', 'buz');
            $emitter->emit('bar', 'buz');
            $emitter->emit('bar', 'buz');
            $emitter->removeAllListeners('foo');
            $emitter->emit('foo', 'buz');
            $emitter->emit('bar', 'buz');
            $emitter->emit('baz', 'buz');
            $emitter->removeAllListeners();
            $emitter->emit('foo', 'buz');
            $emitter->emit('bar', 'buz');
            $emitter->emit('baz', 'buz');

            assert(5 === $called);
          });
        });

        describe('->setMaxListeners($maxListeners)');
        describe('->listeners($event)');
      });

      describe('Events', function() {
        describe('error', function(){
          it('throws if there is no listener for it', function($ctx){
            $emitter = new $ctx->classname();
            try {
              $emitter->emit('error', 'buz');
              assert(false);
            } catch(Exception $err) {
            }

            $called = 0;
            $emitter->on('error', function($err) use(&$called) {
              $called++;
              assert('buz' === $err);
            });
            $emitter->emit('error', 'buz');
            assert(1 === $called);

            $emitter->removeAllListeners();
            try {
              $emitter->emit('error', 'buz');
              assert(false);
            } catch(Exception $err) {
            }
            assert(1 === $called);
          });
        });
        describe('newListener');
        describe('removeListener');
      });
    });

    describe('Static', function() {
      describe('Methods', function() {
        describe('::listenerCount(emitter, event)');
      });
    });

  });

});
