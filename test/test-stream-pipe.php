<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream;
use Ac\Async\Stream\Pipe;

Testa::Spec(function(){

  describe('abstract class Pipe', function() {

    it('is available under "Ac\Async\Stream\Pipe"', function() {
      assert(Pipe::class === 'Ac\Async\Stream\Pipe');
    });

    describe('Instances', function() {
      it('writes data from a readable stream to writeable streams', function(callable $done) {

        $readable = Stream::openThrough();
        $writables = [
          Stream::openThrough(),
          Stream::openThrough()
        ];
        $data = ['foo', 'bar'];

        $called = 0;
        $stopAt = count($writables);

        $onData = function($d) use(&$data, &$called, &$stopAt, &$readable) {
          if($d === null) return;
          assert($d === implode('', $data));
          ++$called;
          if($stopAt === $called) {
            fclose($readable);
          }
        };

        $onDestroy = function() use(&$done, &$writablesUnbind, &$called, &$stopAt) {
          assert($stopAt === $called);
          $done();
        };

        $pipe = new Pipe($readable, $onDestroy);
        foreach($writables as $writeable) {
          Stream::read($writeable, $onData);
          $pipe->add($writeable);
        }

        foreach($data as $d) {
          fwrite($readable, $d);
        }

      });
    });

  });

});
