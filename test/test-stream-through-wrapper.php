<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream\ThroughWrapper;

Testa::Spec(function(){

  describe('class ThroughWrapper', function() {

    it('is available under "Ac\Async\Stream\ThroughWrapper"', function() {
      assert(ThroughWrapper::class === 'Ac\Async\Stream\ThroughWrapper');
    });

    it('lets you write to one side and read from the other side of a stream', function() {
      ThroughWrapper::register();
      $stream = fopen(ThroughWrapper::DEFAULT_PROTOCOL.'://php://temp', 'w+b');
      assert(is_resource($stream));
      $meta = stream_get_meta_data($stream);
      assert(strpos($meta['mode'], 'w+') !== false, 'Mode contains "w+".');
      assert(fwrite($stream, 'teststring') !== false, 'fwrite() succeeds');
      assert(fread($stream, 1024) === 'teststring', 'fread() returns what was previously witten.');
      assert(fstat($stream)['size'] === 0, 'Size of stream is 0.');
      fclose($stream);
      assert(is_resource($stream) === false, 'Resource is closed.');
    });

  });

});
