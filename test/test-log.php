<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Log;

Testa::Spec(function(){

  describe('class Log ($stream = STDOUT)', function() {

    it('is available under "Ac\Async\Log"', function() {
      assert(Log::class === 'Ac\Async\Log');
    });

    describe('->debug()');
    describe('->log()');
    describe('->info()');
    describe('->warn()');
    describe('->error()');
    describe('->fatal()');
    describe('->beep()');

  });

});
