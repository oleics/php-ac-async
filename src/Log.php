<?php

namespace Ac\Async;

use \Exception;
use Ac\Async\Stream;
use Ac\Async\Json;

class Log {

  const VERSION = 1;

  const LEVEL_SILENT = 0;
  const LEVEL_TRACE  = 10;
  const LEVEL_DEBUG  = 20;
  const LEVEL_INFO   = 30;
  const LEVEL_WARN   = 40;
  const LEVEL_ERROR  = 50;
  const LEVEL_FATAL  = 60;

  const LABEL_SILENT = 'silent';
  const LABEL_TRACE  = 'trace';
  const LABEL_DEBUG  = 'debug';
  const LABEL_INFO   = 'info';
  const LABEL_WARN   = 'warn';
  const LABEL_ERROR  = 'error';
  const LABEL_FATAL  = 'fatal';

  protected $levelValues = [
    self::LABEL_SILENT => self::LEVEL_SILENT,
    self::LABEL_TRACE  => self::LEVEL_TRACE,
    self::LABEL_DEBUG  => self::LEVEL_DEBUG,
    self::LABEL_INFO   => self::LEVEL_INFO,
    self::LABEL_WARN   => self::LEVEL_WARN,
    self::LABEL_ERROR  => self::LEVEL_ERROR,
    self::LABEL_FATAL  => self::LEVEL_FATAL
  ];

  protected $levelLabels = [
    self::LEVEL_SILENT => self::LABEL_SILENT,
    self::LEVEL_TRACE  => self::LABEL_TRACE,
    self::LEVEL_DEBUG  => self::LABEL_DEBUG,
    self::LEVEL_INFO   => self::LABEL_INFO,
    self::LEVEL_WARN   => self::LABEL_WARN,
    self::LEVEL_ERROR  => self::LABEL_ERROR,
    self::LEVEL_FATAL  => self::LABEL_FATAL
  ];

  protected $name;
  protected $level = self::LEVEL_INFO;
  protected $stream;
  protected $write;

  protected $baseInfos = [];
  protected $infos = [];
  protected $serializers = [];

  public function __construct(array $options = null, $stream = STDOUT) {
    $this->stream = $stream;

    $this->baseInfos = [
      'pid' => getmypid(),
      'hostname' => gethostname()
    ];

    if(isset($options['level'])) {
      $this->setLevel($options['level']);
    }
    if(isset($options['name'])) {
      $this->baseInfos['name'] = $options['name'];
    }
    if(isset($options['infos'])) {
      foreach($options['infos'] as $key => $value) {
        $this->infos[$key] = $value;
      }
    }
    if(isset($options['serializers'])) {
      foreach($options['serializers'] as $key => $value) {
        $this->serializers[$key] = $value;
      }
    }
  }

  //

  protected function setLevel($nextLevel) {
    $previousLevel = $this->level;
    if(isset($this->levelValues[$nextLevel])) {
      $this->level = $this->levelValues[$nextLevel];
      return $previousLevel;
    }
    if(isset($this->levelLabels[$nextLevel])) {
      $this->level = $nextLevel;
      return $previousLevel;
    }
    throw new Exception('Unknown log-level: '.$nextLevel);
  }

  //

  protected function _log($level, array $args) {
    if($this->level === self::LEVEL_SILENT) return;
    if($this->level > $level) return;

    if(!isset($this->write)) {
      $this->write = Json::write($this->stream);
    }

    $num = count($args);
    if($num === 0) {
      $infos = [ 'level' => $level ];
    } else if($num === 1) {
      if(is_array($args[0])) {
        $infos = array_merge([ 'level' => $level ], $args[0]);
      } else {
        $infos = [ 'level' => $level, 'msg' => $args ];
      }
    } else {
      $infos = [ 'level' => $level, 'msg' => null ];
      if(is_array($args[0])) {
        $infos = array_merge($infos, array_shift($args));
      }
      $infos['msg'] = call_user_func_array('sprintf', $args);
    }
    $infos = array_merge($infos, $this->infos);

    foreach($this->serializers as $key => $value) {
      if(isset($infos[$key])) {
        $infos[$key] = call_user_func($value, $infos[$key]);
      }
    }

    call_user_func($this->write, array_merge($this->baseInfos, $infos));
  }

  public function trace() {
    $this->_log(self::LEVEL_TRACE, func_get_args());
  }

  public function debug() {
    $this->_log(self::LEVEL_DEBUG, func_get_args());
  }

  public function info() {
    $this->_log(self::LEVEL_INFO, func_get_args());
  }

  public function warn() {
    $this->beep();
    $this->_log(self::LEVEL_WARN, func_get_args());
  }

  public function error() {
    $this->beep();
    $this->beep();
    $this->_log(self::LEVEL_ERROR, func_get_args());
  }

  public function fatal() {
    $this->beep();
    $this->beep();
    $this->beep();
    $this->_log(self::LEVEL_FATAL, func_get_args());
    exit(1);
  }

  public function beep() {
    echo "\x07";
    flush();
  }

}
