<?php

namespace /* global */ {

  use Ac\Async\Async;

  /**
   * Execute callable `$fn`.
   *
   * @param callable $fn
   * @param int $priority
   * @param array $args
   * @return void
   */
  function async(callable $fn, $priority = 0, array $args = null) {
    Async::getEngine()->enqueue($fn, $args, $priority);
  }

  /**
   * @param callable $fn
   * @param int $forFrame
   * @param array $args
   * @return void
   */
  function async_schedule(callable $fn, $forFrame = 0, array $args = null) {
    Async::getEngine()->schedule($fn, $args, $forFrame);
  }

  /**
   * @param callable $fn
   * @param int $eachFrame
   * @param array $args
   * @return void
   */
  function async_scheduleEach(callable $fn, $eachFrame = 0, array $args = null) {
    Async::getEngine()->scheduleEach($fn, $args, $eachFrame);
  }

  /**
   * @param callable $fn
   * @param real $seconds
   * @param array $args
   * @return void
   */
  function async_setTimeout(callable $fn, $seconds = 0.0, array $args = null) {
    return Async::getEngine()->setTimeout($fn, $args, $seconds);
  }

  /**
   * @param callable $fn
   * @param real $seconds
   * @param array $args
   * @return void
   */
  function async_setInterval(callable $fn, $seconds = 0.0, array $args = null) {
    return Async::getEngine()->setInterval($fn, $args, $seconds);
  }

  /**
   * @param callable $fn
   * @return void
   */
  function async_removeFromSchedules(callable $fn) {
    Async::getEngine()->removeFromSchedules($fn);
  }

}

namespace Ac\Async {

  use \Exception;
  use Ac\Async\Kernel;
  use Ac\Async\Engine;

  abstract class Async {

    const STATE_KERNEL_STOPPED  = 1;
    const STATE_KERNEL_STOPPING = 2;
    const STATE_KERNEL_EMPTY    = 3;
    const STATE_KERNEL_RUNNING  = 4;

    const STATE_ENGINE_STOPPED  = 5;
    const STATE_ENGINE_STOPPING = 6;
    const STATE_ENGINE_EMPTY    = 7;
    const STATE_ENGINE_RUNNING  = 8;

    static protected $kernel_framerate;
    static protected $kernel_streamSelect;
    static protected $kernel_defaultToFileMode = false;
    static protected $engine_framerate;

    static protected $kernel;
    static protected $engine;
    static protected $blocks = 0;
    static protected $state;

    //

    /**
     *  @param real|null $engine_framerate
     *  @param real|null $kernel_framerate
     *  @param bool $kernel_defaultToFileMode
     *
     *  @return void
     */
    static public function configure($engine_framerate = null, $kernel_framerate = null, $kernel_defaultToFileMode = false) {
      self::$engine_framerate = $engine_framerate;
      self::$kernel_framerate = $kernel_framerate;
      self::$kernel_defaultToFileMode = $kernel_defaultToFileMode;
    }

    //

    /**
     * Wrap a file.
     * @return void
     */
    static public function wrap($filename) {
      self::blockStart();
      require($filename);
      self::blockEnd();
    }

    //

    static public function blockStart() {
      ++self::$blocks;
      self::boot();
    }

    static public function blockEnd() {
      --self::$blocks;
      if(self::$blocks === 0) {
        self::run();
      }
    }

    //

    static protected function boot() {
      if(!isset(self::$kernel)) {
        self::$kernel = new Kernel(self::$kernel_framerate, self::$kernel_streamSelect, self::$kernel_defaultToFileMode);
      }
      if(!isset(self::$engine)) {
        self::$engine = new Engine(self::$engine_framerate, self::$kernel);
      }
    }

    static protected function run(callable $fn = null) {
      if(self::$kernel->isRunning) return;
      self::start();
    }

    static protected function start() {
      if(self::$kernel->isRunning) {
        throw new Exception('Already running.');
      }

      self::$engine->start(function() {
        // echo "\nengine tick\n";
      });

      $stopWait = 0;
      $stopWaitMax = (int) ceil(self::$kernel->framerate / self::$engine->framerate) * 2;
      // $stopWaitMax = 0;

      self::$kernel->start(function() use(&$stopWait, &$stopWaitMax) {
        // echo "\nkernel tick\n";

        // Figure state, jump from one state to another
        switch(self::$state) {
          case self::STATE_KERNEL_STOPPED:
            break;

          case self::STATE_KERNEL_STOPPING:
            if(self::$engine->isEmpty()) {
              if(self::$kernel->isEmpty()) {
                ++$stopWait;
                if($stopWait >= $stopWaitMax) {
                  echo "\nkernel stop ($stopWaitMax)\n";
                  self::$kernel->stop();
                  self::$state = self::STATE_KERNEL_STOPPED;
                }
              } else {
                self::$state = self::STATE_KERNEL_RUNNING;
              }
            } else {
              self::$state = self::STATE_ENGINE_STOPPED;
            }
            break;

          case self::STATE_KERNEL_EMPTY:
            if(self::$engine->isEmpty()) {
              if(self::$kernel->isEmpty()) {
                $stopWait = 0;
                self::$state = self::STATE_KERNEL_STOPPING;
              } else {
                self::$state = self::STATE_KERNEL_RUNNING;
              }
            } else {
              self::$state = self::STATE_ENGINE_STOPPED;
            }
            break;

          case self::STATE_KERNEL_RUNNING:
            if(self::$engine->isEmpty()) {
              if(self::$kernel->isEmpty()) {
                self::$state = self::STATE_KERNEL_EMPTY;
              }
            } else {
              self::$state = self::STATE_ENGINE_STOPPED;
            }
            break;

          case self::STATE_ENGINE_STOPPED:
            if(self::$engine->isEmpty()) {
              self::$state = self::STATE_KERNEL_RUNNING;
            } else {
              // echo "\nengine start\n";
              self::$engine->start();
              self::$state = self::STATE_ENGINE_RUNNING;
            }
            break;

          case self::STATE_ENGINE_STOPPING:
            if(self::$engine->isEmpty()) {
              ++$stopWait;
              if($stopWait >= $stopWaitMax) {
                // echo "\nengine stop ($stopWaitMax)\n";
                self::$engine->stop();
                self::$state = self::STATE_ENGINE_STOPPED;
              }
            } else {
              self::$state = self::STATE_ENGINE_RUNNING;
            }
            break;

          case self::STATE_ENGINE_EMPTY:
            if(self::$engine->isEmpty()) {
              $stopWait = 0;
              self::$state = self::STATE_ENGINE_STOPPING;
            } else {
              self::$state = self::STATE_ENGINE_RUNNING;
            }
            break;

          case self::STATE_ENGINE_RUNNING:
          default:
            if(self::$engine->isEmpty()) {
              self::$state = self::STATE_ENGINE_EMPTY;
            }
            break;
        }

      }); // blocks
    }

    //

    static public function &getEngine() {
      return self::$engine;
    }

    static public function &getKernel() {
      return self::$kernel;
    }

    static public function &getSelect() {
      return self::$kernel->getSelect();
    }

  }

}
