<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream;

Testa::Spec(function(){

  describe('class Stream', function() {

    it('is available under "Ac\Async\Stream"', function() {
      assert(Stream::class === 'Ac\Async\Stream');
    });

    describe('Used Traits', function() {

      $testTrait = function($trait) {
        it('uses trait "'.$trait.'"', function() use(&$trait) {
          $usedTraits = array_values(class_uses('Ac\Async\Stream'));
          assert(array_search($trait, $usedTraits, true) !== false, 'Trait "'.$trait.'" is used.');
        });
      };

      $expectedTraits = [
        'Ac\Async\Stream\CommonTrait',
        'Ac\Async\Stream\ProcessTrait',
        'Ac\Async\Stream\ReadTrait',
        'Ac\Async\Stream\WriteTrait',
      ];

      foreach($expectedTraits as $expectedTrait) {
        $testTrait($expectedTrait);
      }

    });

  });

});
