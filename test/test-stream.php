<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream;

Testa::Spec(function(){

  describe('abstract class Stream', function() {

    it('is available under "Ac\Async\Stream"', function() {
      assert(Stream::class === 'Ac\Async\Stream');
    });

    testUsedTraits('Ac\Async\Stream', [
      'Ac\Async\Stream\CommonTrait',
      'Ac\Async\Stream\ProcessTrait',
      'Ac\Async\Stream\ReadTrait',
      'Ac\Async\Stream\WriteTrait',
      'Ac\Async\Stream\PipeTrait',
    ]);

  });

});
