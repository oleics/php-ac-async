<?php

namespace Ac\Async\Stream;

class ThroughWrapper {
  const DEFAULT_PROTOCOL = 'streamthrough';

  protected $bytesWritten = 0;
  protected $bytesRead = 0;

  public function stream_open($url, $mode, $options, &$opened_path) {
    $scheme = parse_url($url, PHP_URL_SCHEME);
    $url = substr($url, strlen($scheme)+3);

    $this->stream = fopen($url, 'w+b');

    return true;
  }

  public function stream_read($count) {
    $data = fread($this->stream, $count);
    $this->bytesRead += strlen($data);
    if($this->bytesWritten <= $this->bytesRead) {
      ftruncate($this->stream, 0);
      $this->bytesWritten = 0;
      $this->bytesRead = 0;
    }
    return $data;
  }

  public function stream_write($data) {
    $pos = ftell($this->stream);
    fseek($this->stream, fstat($this->stream)['size'], SEEK_SET);
    $bytes = fwrite($this->stream, $data);
    fseek($this->stream, $pos, SEEK_SET);
    $this->bytesWritten += $bytes;
    return $bytes;
  }

  public function stream_tell() {
    return ftell($this->stream);
  }

  public function stream_flush() {
    return fflush($this->stream);
  }

  public function stream_eof() {
    return feof($this->stream);
  }

  public function stream_close() {
    return fclose($this->stream);
  }

  public function stream_seek($offset, $whence) {
    return fseek($this->stream, $offset, $whence);
  }

  public function stream_truncate($newSize) {
    return ftruncate($this->stream, $newSize);
  }

  public function stream_stat() {
    return fstat($this->stream);
  }

  public function stream_set_option($option, $arg1, $arg2) {
    if($option === STREAM_OPTION_BLOCKING) {
      return stream_set_blocking($this->stream, $arg1);
    }
    if($option === STREAM_OPTION_READ_TIMEOUT) {
      return stream_set_timeout($this->stream, $arg1, $arg2);
    }
    if($option === STREAM_OPTION_WRITE_BUFFER) {
      return stream_set_write_buffer($this->stream, $arg2);
    }
    return false;
  }

  public function stream_cast($cast_as) {
    if($cast_as === STREAM_CAST_FOR_SELECT) {
      return $this->stream;
    }
    if($cast_as === STREAM_CAST_AS_STREAM) {
      return $this->stream;
    }
    return false;
  }

  // Static

  static public function register($protocol = ThroughWrapper::DEFAULT_PROTOCOL) {
    if(in_array($protocol, stream_get_wrappers())) stream_wrapper_unregister($protocol);
    stream_wrapper_register($protocol, self::class);
  }
}
