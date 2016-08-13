<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Stream\CommonTrait;

Testa::Spec(function(){

  describe('trait Stream\CommonTrait', function() {

    before(function($ctx){
      $ctx->classname = createClassWithTrait('Ac\Async\Stream\CommonTrait');
    });

    after(function($ctx){
      unset($ctx->classname);
    });

    it('is available under "Ac\Async\Stream\CommonTrait"', function() {
      assert(CommonTrait::class === 'Ac\Async\Stream\CommonTrait');
    });

    describe('::openProcess($cmd)', function() {
      it('returns a resource which is readable', function($ctx) {
        $r = call_user_func([$ctx->classname, 'openProcess'], 'php -r "echo \"teststring\";"');
        assert(is_resource($r));
        $meta = stream_get_meta_data($r);
        assert(strpos($meta['mode'], 'r') !== false, 'Mode contains "r".');
        assert(fread($r, 1024) === 'teststring', 'fread() returns "teststring".');
        fclose($r);
        assert(is_resource($r) === false, 'Resource is closed.');
      });
    });

    describe('::openWritable($url = \'php://temp\')', function() {
      it('returns a resource which is writable', function($ctx) {
        $r = call_user_func([$ctx->classname, 'openWritable']);
        assert(is_resource($r));
        $meta = stream_get_meta_data($r);
        assert(strpos($meta['mode'], 'w') !== false);
        assert(fwrite($r, 'test') !== false);
        fclose($r);
        assert(is_resource($r) === false, 'Resource is closed.');
      });
    });

    describe('::openReadable($url = \'php://temp\')', function() {
      it('returns a resource which is readable', function($ctx) {
        $r = call_user_func([$ctx->classname, 'openReadable']);
        assert(is_resource($r));
        $meta = stream_get_meta_data($r);
        assert(strpos($meta['mode'], 'r') !== false, 'Mode contains "r".');
        assert(fread($r, 1024) !== false, 'read() succeeds');
        fclose($r);
        assert(is_resource($r) === false, 'Resource is closed.');
      });
    });

    describe('::openDuplex($url = \'php://temp\')', function() {
      it('returns a resource which is read- and writable', function($ctx) {
        $r = call_user_func([$ctx->classname, 'openDuplex']);
        assert(is_resource($r));
        $meta = stream_get_meta_data($r);
        assert(strpos($meta['mode'], 'w+') !== false, 'Mode contains "w+".');
        assert(fwrite($r, 'teststring') !== false, 'fwrite() succeeds');
        assert(rewind($r), 'rewind() succeeds');
        assert(fread($r, 1024) === 'teststring', 'fread() returns what was previously witten.');
        fclose($r);
        assert(is_resource($r) === false, 'Resource is closed.');
      });
    });

    describe('::openThrough($url = \'php://temp\')', function() {
      it('returns a resource which is read- and writable', function($ctx) {
        $r = call_user_func([$ctx->classname, 'openThrough']);
        assert(is_resource($r));
        $meta = stream_get_meta_data($r);
        assert(strpos($meta['mode'], 'w+') !== false, 'Mode contains "w+".');
        assert(fwrite($r, 'teststring') !== false, 'fwrite() succeeds');
        assert(fread($r, 1024) === 'teststring', 'fread() returns what was previously witten.');
        fclose($r);
        assert(is_resource($r) === false, 'Resource is closed.');
      });
    });


  });

});
