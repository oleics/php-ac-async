<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\StringParser;

Testa::Spec(function(){

  describe('class StringParser ($delim = "\n")', function() {

    it('is available under "Ac\Async\StringParser"', function() {
      assert(StringParser::class === 'Ac\Async\StringParser');
    });

    describe('->write($str)');
    describe('->end()');
    describe('->bufferAppend($str)');
    describe('->bufferClear()');
    // describe('(generator) ::chars($str)');
    // describe('::nextchar($str, &$pointer)');

  });

});
