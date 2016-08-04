<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream\ProcessTrait;

Testa::Spec(function(){

  describe('trait Stream\ProcessTrait', function() {

    before(function($ctx){
      $ctx->classname = createClassWithTrait('Ac\Async\Stream\ProcessTrait');
    });

    after(function($ctx){
      unset($ctx->classname);
    });

    it('is available under "Ac\Async\Stream\ProcessTrait"', function() {
      assert(ProcessTrait::class === 'Ac\Async\Stream\ProcessTrait');
    });

    describe('::spawnProcess($cmd)', function() {
      it('returns an instance of Ac\Async\Process', function($ctx) {
        $r = call_user_func([$ctx->classname, 'spawnProcess'], 'php -r "echo \"teststring\";"');
        assert($r instanceof Ac\Async\Process);
      });
    });

  });

});
