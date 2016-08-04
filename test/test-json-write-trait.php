<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Json\WriteTrait;

Testa::Spec(function(){

  describe('trait Json\WriteTrait', function() {

    before(function($ctx){
      $ctx->classname = createClassWithTrait('Ac\Async\Json\WriteTrait');
    });

    after(function($ctx){
      unset($ctx->classname);
    });

    it('is available under "Ac\Async\Json\WriteTrait"', function() {
      assert(WriteTrait::class === 'Ac\Async\Json\WriteTrait');
    });

    describe('::write($stream)');

  });

});
