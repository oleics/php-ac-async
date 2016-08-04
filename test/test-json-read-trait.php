<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Json\ReadTrait;

Testa::Spec(function(){

  describe('trait Json\ReadTrait', function() {

    before(function($ctx){
      $ctx->classname = createClassWithTrait('Ac\Async\Json\ReadTrait');
    });

    after(function($ctx){
      unset($ctx->classname);
    });

    it('is available under "Ac\Async\Json\ReadTrait"', function() {
      assert(ReadTrait::class === 'Ac\Async\Json\ReadTrait');
    });

    describe('::read($stream, callable $callback, callable $callbackData = null)');

  });

});
