<?php

namespace Ac\Async;

use \SplFixedArray;
use \SplQueue;
use Ac\Async\Kernel;

class Engine {

  const PRIORITY_HIGHEST = 0;
  const PRIORITY_HIGHER  = 2;
  const PRIORITY_HIGH    = 5;
  const PRIORITY_NORMAL  = 10;
  const PRIORITY_LOW     = 100;
  const PRIORITY_LOWER   = 500;
  const PRIORITY_LOWEST  = 1000;

  protected $kernel;
  protected $kernelCallback;

  /**
   *  @var real
   */
  public $framerate = 0.0167; // 60 fps

  /**
   *  @var real
   */
  public $frame     = 0;

  protected $framerate_eachKernelFrame;

  protected $queueSize = 0;
  protected $queuePriority;
  protected $queue;
  protected $schedulePerFrame = [];
  protected $scheduleForEachFrame = [];

  public function __construct($framerate = null, Kernel &$kernel = null) {
    $this->queuePriority = new SplFixedArray();
    $this->queue = [];
    $this->addPriority(Engine::PRIORITY_HIGHEST);
    $this->addPriority(Engine::PRIORITY_HIGHER);
    $this->addPriority(Engine::PRIORITY_HIGH);
    $this->addPriority(Engine::PRIORITY_NORMAL);
    $this->addPriority(Engine::PRIORITY_LOW);
    $this->addPriority(Engine::PRIORITY_LOWER);
    $this->addPriority(Engine::PRIORITY_LOWEST);

    if(isset($framerate)) $this->framerate = $framerate;
    if(!isset($kernel)) {
      $kernel = new Kernel($this->framerate);
    }
    $this->setKernel($kernel);
  }

  //

  public function getKernel() {
    return $this->kernel;
  }

  public function setKernel(Kernel &$kernel) {
    if(isset($this->kernelCallback)) {
      $this->kernel->removeCallback($this->kernelCallback);
    }
    $this->kernel =& $kernel;
    $this->changeFramerate($this->framerate);
    if(isset($this->kernelCallback)) {
      $this->kernel->addCallback($this->kernelCallback);
    }
  }

  //

  public function changeFramerate($framerate) {
    $this->framerate = $framerate;
    $this->framerate_eachKernelFrame = ceil(($this->framerate / $this->kernel->framerate));
  }

  //

  public function start(callable $onFrame = null) {
    $this->time = microtime(true);
    $this->kernelCallback = function(Kernel &$kernel) use(&$onFrame) {
      $this->onKernelFrame($onFrame);
    };
    $this->kernel->addCallback($this->kernelCallback);
  }

  public function stop() {
    if(!isset($this->kernelCallback)) return false;
    $this->kernel->removeCallback($this->kernelCallback);
    $this->kernelCallback = null;
    return true;
  }

  protected function onKernelFrame(callable &$onFrame = null) {
    if($this->kernel->frame % $this->framerate_eachKernelFrame !== 0) return;

    $timeStart = microtime(true);

    ++$this->frame;

    $args = [];
    if(isset($onFrame)) {
      $onFrameArgs = [&$this];
    }

    $frame = $this->frame;
    $kernelFramerate = $this->kernel->framerate;

    // schedule
    if(isset($this->schedulePerFrame[$frame])) {
      $this->schedulePerFrame[$frame] = array_reverse($this->schedulePerFrame[$frame]);
      while(($cb = array_pop($this->schedulePerFrame[$frame])) !== null) {
        call_user_func_array($cb[0], isset($cb[1]) ? $cb[1] : $args);
      }
      unset($this->schedulePerFrame[$frame]);
    }

    // scheduleEach
    $len = count($this->scheduleForEachFrame);
    for($i=0; $i<$len; $i++) {
      $val =& $this->scheduleForEachFrame[$i];
      $val[2] = $val[2] + 1;
      if($val[1] === $val[2]) {
        $val[2] = 0;
        call_user_func_array($val[0], isset($val[3]) ? $val[3] : $args);
      }
      unset($val);
    }

    // user-code
    if(isset($onFrame)) {
      call_user_func_array($onFrame, $onFrameArgs);
    }

    // dequeue prioritized blocks of functions
    $this->queuePriority->rewind();
    while($this->queuePriority->valid()) {
      $queue = $this->queue[$this->queuePriority->current()];
      while(!$queue->isEmpty()) {
        --$this->queueSize;
        $cb = $queue->dequeue();
        call_user_func_array($cb[0], isset($cb[1]) ? $cb[1] : $args);
      }

      // break if previous code already consumed all available kernel-frame-time
      if(microtime(true) - $timeStart >= $kernelFramerate) {
        break(1);
      }

      $this->queuePriority->next();
    }
  }

  //

  public function isEmpty() {
    if($this->queueSize > 0) return false;
    if(!empty($this->schedulePerFrame)) return false;
    if(!empty($this->scheduleForEachFrame)) return false;
    return true;
  }

  // Prio Func Queue

  public function enqueue(callable $fn, array $args = null, $priority = Engine::PRIORITY_NORMAL) {
    if(isset($args)) {
      $d = new SplFixedArray(2);
      $d[0] = $fn;
      $d[1] = $args;
    } else {
      $d = new SplFixedArray(1);
      $d[0] = $fn;
    }
    if(!isset($priority)) $priority = Engine::PRIORITY_NORMAL;
    ++$this->queueSize;
    $this->queue[$priority]->enqueue($d);
  }

  protected function addPriority($priority) {
    if(!is_int($priority)) {
      throw new Exception('Priority must be an integer, got "'.gettype($priority).'".');
    }
    if(isset($this->queue[$priority])) {
      throw new Exception('Priority "'.$priority.'" already exists.');
    }
    $arr = $this->queuePriority->toArray();
    $arr[] = $priority;
    sort($arr, SORT_NATURAL);
    $this->queuePriority = SplFixedArray::fromArray($arr);
    $this->queue[$priority] = new SplQueue();
  }

  // Schedules

  public function schedule(callable $fn, array $args = null, $forFrame = 0) {
    $forFrame = $this->frame + $forFrame;
    if($forFrame <= $this->frame) {
      $forFrame = $this->frame + 1;
    }

    if(isset($args)) {
      $d = new SplFixedArray(2);
      $d[0] = $fn;
      $d[1] = $args;
    } else {
      $d = new SplFixedArray(1);
      $d[0] = $fn;
    }

    if(!isset($this->schedulePerFrame[$forFrame])) {
      $this->schedulePerFrame[$forFrame] = [];
    }
    $this->schedulePerFrame[$forFrame][] = $d;
  }

  public function scheduleEach(callable $fn, array $args = null, $eachFrame = 0) {
    if($eachFrame <= 0) $eachFrame = 1;

    if(isset($args)) {
      $d = new SplFixedArray(4);
      $d[0] = $fn;
      $d[3] = $args;
    } else {
      $d = new SplFixedArray(3);
      $d[0] = $fn;
    }
    $d[1] = $eachFrame;
    $d[2] = 0;

    $this->scheduleForEachFrame[] = $d;
  }

  public function removeFromSchedules(callable $fn) {
    $found = false;

    $keys = array_keys($this->schedulePerFrame);
    $klen = count($keys);
    for($i=0; $i<$klen; $i++) {
      $vals =& $this->schedulePerFrame[$keys[$i]];
      $len = count($vals);
      for($ii=0; $ii<$len; $ii++) {
        if($fn === $vals[$ii][0]) {
          array_splice($vals, $ii, 1);
          --$len;
          if($found === false) $found = true;
        }
      }
      if(empty($vals)) {
        unset($this->schedulePerFrame[$keys[$i]]);
      }
      unset($vals);
    }

    $len = count($this->scheduleForEachFrame);
    for($i=0; $i<$len; $i++) {
      if($fn === $this->scheduleForEachFrame[$i][0]) {
        array_splice($this->scheduleForEachFrame, $i, 1);
        --$len;
        if($found === false) $found = true;
      }
    }

    return $found;
  }

  // Timers

  public function setTimeout(callable $fn, array $args = null, $seconds = 0.0) {
    // echo "$this->frame ".($this->frame + ceil($seconds / $this->framerate))."\n";
    $this->schedule($fn, $args, (int) ceil($seconds / $this->framerate));
  }

  public function setInterval(callable $fn, array $args = null, $seconds = 0.0) {
    // echo "$seconds ".$this->framerate." ".ceil($seconds / $this->framerate)."\n";
    $this->scheduleEach($fn, $args, (int) ceil($seconds / $this->framerate));
  }

}
