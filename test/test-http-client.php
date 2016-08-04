<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ac\Testa\Testa;
use Ac\Async\Http\Client;

Testa::Spec(function(){

  describe('class Client', function() {

    it('is available under "Ac\Async\Http\Client"', function() {
      assert(Client::class === 'Ac\Async\Http\Client');
    });

  });

});
