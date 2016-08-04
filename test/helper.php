<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ac\Async\Stream;

function startTestWebserver(&$ctx, callable &$done, $host = null, $filepath = null) {
  if(!isset($host)) $host = 'localhost:8000';
  if(!isset($filepath)) $filepath = __DIR__.'/www/index.php';

  $ctx->serverHost = $host;
  $ctx->serverFilepath = $filepath;
  if(is_file($filepath)) {
    $ctx->server = $server = Stream::spawnProcess('php -S '.$host.' '.$filepath);
  } else {
    $ctx->server = $server = Stream::spawnProcess('php -S '.$host.' -t '.$filepath);
  }

  assert(is_resource($server->stdin));
  assert(is_resource($server->stdout));
  assert(is_resource($server->stderr));

  $started = false;

  $server->read(function($data) use(&$started) {
    if($data === null) return;
    if($started) return;
    fwrite(STDERR, "\nserver-process: $data\n");
  });

  $check;
  $check = function() use(&$check, &$started, &$ctx, &$done) {
    if(@get_headers('http://'.$ctx->serverHost)) {
      $started = true;
      async(function() use(&$done){
        $done();
      });
      return;
    }
    async_setTimeout($check, 0.3);
  };
  async($check);
}

function stopTestWebserver(&$ctx) {
  $ctx->server->kill();
  unset($ctx->server);
  unset($ctx->serverFilepath);
  unset($ctx->serverHost);
}

function createClassWithTrait($traitName) {
  static $num = 0;
  ++$num;
  $classname = 'AcAsyncTestHelper_DynamicClassWithTrait_'.$num.'';
  eval('class '.$classname.' { use '.$traitName.'; }');
  return $classname;
}


function testUsedTraits($classname, array $expectedTraits) {
  describe('Traits of '.$classname.'', function() use(&$classname, &$expectedTraits) {
    $testTrait = function($trait) use(&$classname) {
      it(''.$trait.'', function() use(&$classname, &$trait) {
        $usedTraits = array_values(class_uses($classname));
        assert(array_search($trait, $usedTraits, true) !== false, 'Trait "'.$trait.'" is used.');
      });
    };

    foreach($expectedTraits as $expectedTrait) {
      $testTrait($expectedTrait);
    }
  });
}
