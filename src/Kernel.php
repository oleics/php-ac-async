<?php

namespace Ac\Async;

use \Exception;
use Ac\Async\Select;
use Ac\Async\StringParser;
use Ac\Async\Json;
use Ac\Async\Log;

use Ac\Async\Kernel\LogTrait;

/**
 * The kernel.
 */
class Kernel {

  use LogTrait;

  const MODE_SOCKET = 'socket';
  const MODE_FILE   = 'file';
  const READ_BYTES_MAX = Select::CHUNK_SIZE;

  public $mode;

  protected $socketPair;
  protected $stream;
  protected $inputParser;

  protected $select;

  public $isRunning = false;

  public $framerate = 0.0167; // 60 fps
  public $frame = 0;

  protected $time = 0.0;
  public $timeElapsedFrame = 0.0;
  public $timeElapsedTotal = 0.0;
  public $timeDrift = 0.0;
  public $successiveNegativeTimeDrifts = 0;

  protected $callbacks = [];
  protected $callbacksLen = 0;

  public function __construct($framerate = null, Select &$select = null, $defaultToFileMode = false) {
    if(isset($framerate)) $this->framerate = $framerate;

    if(isset($log)) {
      $this->log =& $log;
    } else {
      $this->log = new Log();
    }

    if(isset($select)) {
      $this->select =& $select;
    } else {
      $this->select = new Select();
    }

    $someModeSupport = false;
    if($defaultToFileMode) {
      if(($someModeSupport = $this->enableFileMode()) === false) {
        $someModeSupport = $this->enableSocketMode();
      }
    } else if(($someModeSupport = $this->enableSocketMode()) === false) {
      $someModeSupport = $this->enableFileMode();
    }
    if($someModeSupport === false) {
      throw new Exception('Unable to run Kernel.');
    }

    $this->inputParser = new StringParser("\n");
  }

  //

  public function isEmpty() {
    if(count($this->callbacks) !== 0) return false;
    if($this->select->numReadables() > 1) return false;
    if($this->select->numWritables() > 1) return false;
    if($this->select->numExceptables() > 0) return false;
    return true;
  }

  public function &getSelect() {
    return $this->select;
  }

  // Mode: Socket-Pair or Single Temporary File

  protected function enableSocketMode() {
    $this->socketPair = @stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
    if($this->socketPair === false) {
      return false;
    }
    $this->select->addCallbackWritable(function(&$writable) {
      $this->writable($writable);
    }, $this->socketPair[0]);
    $this->select->addCallbackReadable(function(&$readable) {
      $this->readable($readable);
    }, $this->socketPair[1]);
    $this->mode = self::MODE_SOCKET;
    return true;
  }

  protected function enableFileMode() {
    $this->stream = @fopen('php://temp', 'bw+');
    if($this->stream === false) {
      return false;
    }
    $this->select->addCallbackWritable(function(&$writable) {
      $this->writable($writable);
    }, $this->stream);
    $this->select->addCallbackReadable(function(&$readable) {
      $this->readable($readable);
    }, $this->stream);
    $this->mode = self::MODE_FILE;
    return true;
  }

  // start, stop, step

  public function start(callable $onFrame = null) { // blocks
    $this->isRunning = true;
    $this->time = microtime(true);
    $args = [&$this];
    // $this->debug('kernel start');

    // loop
    while($this->isRunning === true && $this->select->select()) {
      if(isset($onFrame)) {
        call_user_func_array($onFrame, $args);
      }

      if($this->callbacksLen !== 0) {
        for($i=0; $i<$this->callbacksLen; $i++) {
          call_user_func_array($this->callbacks[$i], $args);
        }
      }

      $this->calculateTimeAndCompensateDrift();
      $this->frameInfos();
    }

  }

  public function step() {
    if($this->isRunning === true) throw new Exception('Stop before step.');
    $this->select->select();
  }

  public function stop() {
    if($this->isRunning !== true) return false;
    $this->isRunning = false;
    return true;
  }

  //

  protected function calculateTimeAndCompensateDrift() {
    // calculate
    $time = microtime(true);
    $this->timeElapsedFrame = $time - $this->time;
    $this->timeElapsedTotal += $this->timeElapsedFrame;
    $this->time = $time;

    $this->timeDrift = ($this->frame * $this->framerate) - $this->timeElapsedTotal;

    // compensate
    if($this->timeDrift >= 1e-6) {
      $this->successiveNegativeTimeDrifts = 0;
      usleep($this->timeDrift * 1e6);
    } else if($this->timeDrift <= -1e-6) {
      $this->successiveNegativeTimeDrifts++;
    }
  }

  protected function writable($stream) {
    ++$this->frame;

    if($this->mode === self::MODE_FILE) {
      ftruncate($stream, 0);
      fseek($stream, 0);
    }

    fwrite($stream, $this->frame."\n");
    // fwrite($stream, Json::encode([
    //   'frame' => $this->frame
    // ])."\n");

    if($this->mode === self::MODE_FILE) {
      fseek($stream, 0);
    }
  }

  protected function readable($stream) {
    $iter = $this->inputParser->write(fread($stream, self::READ_BYTES_MAX));
    foreach($iter as $frame) {
      $frame = (int) $frame;
      if($frame < $this->frame) {
        throw new Exception("Frames are out-of-sync: Input-frame is $frame, local-frame is $this->frame.");
      }
    }
    // foreach($iter as $d) {
    //   $d = Json::decode($d);
    //   if($d['frame'] < $this->frame) {
    //     throw new Exception("Frames are out-of-sync: Input-frame is ${d['frame']}, local-frame is $this->frame.");
    //   }
    // }
  }

  //

  public function addCallback(callable $fn) {
    $this->callbacks[] = $fn;
    $this->callbacksLen++;
  }

  public function removeCallback(callable $fn) {
    $index = array_search($fn, $this->callbacks);
    if($index === false) return false;
    array_splice($this->callbacks, $index, 1);
    $this->callbacksLen--;
    return true;
  }

}
