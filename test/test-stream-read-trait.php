<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream;
use Ac\Async\Stream\ReadTrait;

Testa::Spec(function(){

  describe('trait Stream\ReadTrait', function() {

    it('is available under "Ac\Async\Stream\ReadTrait"', function() {
      assert(trait_exists('Ac\Async\Stream\ReadTrait'));
      assert(ReadTrait::class === 'Ac\Async\Stream\ReadTrait');
    });

    describe('Static Functions', function(){
      before(function($ctx){
        $ctx->classname = createClassWithTrait('Ac\Async\Stream\ReadTrait');
      });

      after(function($ctx){
        unset($ctx->classname);
      });

      before('start webserver', function(&$ctx, callable $done) {
        startTestWebserver($ctx, $done, 'localhost:8000', __DIR__.'/www/');
      });

      after('stop webserver', function(&$ctx) {
        stopTestWebserver($ctx);
      });

      describe('::read($stream, callable $callback)', function() {
        it('reads a readable stream', function($ctx, callable $done) {
          $expectedBytes = 300;
          $bytes = 0;
          $stream = Stream::openReadable('http://'.$ctx->serverHost.'/bytes.php?bytes='.$expectedBytes);
          $callback = function($data) use(&$expectedBytes, &$bytes, &$done) {
            if($data === null) {
              assert($expectedBytes === $bytes, "expectedBytes $expectedBytes === $bytes");
              return $done();
            }
            $bytes += strlen($data);
            assert($expectedBytes >= $bytes, "expectedBytes $expectedBytes >= $bytes");
          };
          $r = call_user_func([$ctx->classname, 'read'], $stream, $callback);
        });
      });

      describe('::readLines($stream, callable $callback)', function() {
        it('reads lines from a readable stream', function($ctx, callable $done) {
          $expectedLines = 300;
          $lines = 0;
          $stream = Stream::openReadable('http://'.$ctx->serverHost.'/lines.php?lines='.$expectedLines);
          $callback = function($line) use(&$expectedLines, &$lines, &$done) {
            if($line === null) {
              assert($expectedLines === $lines, "expectedLines $expectedLines === $lines");
              return $done();
            }
            $lines++;
            assert($expectedLines >= $lines, "expectedLines $expectedLines >= $lines");
            if($expectedLines > $lines) {
              assert("\n" === substr($line, -1));
            }
          };
          $r = call_user_func([$ctx->classname, 'readLines'], $stream, $callback);
        });
      });

    });

  });

});
