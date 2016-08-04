<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Json;

Testa::Spec(function(){

  describe('abstract class Json', function() {

    it('is available under "Ac\Async\Json"', function() {
      assert(Json::class === 'Ac\Async\Json');
    });

    testUsedTraits('Ac\Async\Json', [
      'Ac\Async\Json\ReadTrait',
      'Ac\Async\Json\WriteTrait',
    ]);

  });

});
