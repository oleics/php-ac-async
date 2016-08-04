<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/helper.php';

use Ac\Testa\Testa;
use Ac\Async\Process;

Testa::Spec(function(){

  describe('class Process ($cmd)', function() {

    it('is available under "Ac\Async\Process"', function() {
      assert(Process::class === 'Ac\Async\Process');
    });

    describe('Constants', function() {
      describe('SIGNAL_SIGTERM');
    });

    describe('Instance', function() {
      describe('Properties', function() {
        describe('Streams', function() {
          describe('->stdin');
          describe('->stdout');
          describe('->stderr');
        });

        describe('Status', function() {
          describe('->command');
          describe('->pid');
          describe('->running');
          describe('->signaled');
          describe('->stopped');
          describe('->exitcode');
          describe('->termsig');
          describe('->stopsig');
        });
      });

      describe('Methods', function() {
        describe('->kill($signal = Process::SIGNAL_SIGTERM)');
        describe('->close()');

        describe('Convenience', function() {
          describe('->readStdout(callable $callback)');
          describe('->readStderr(callable $callback)');
          describe('->read(callable $callback)');
          describe('->write($data, callable $callback = null)');
        });
      });
    });

    describe('Static', function() {
      describe('Methods', function() {
        describe('::spawn($cmd)');
      });
    });

  });

});
