<?php

namespace Ac\Async;

class StringParser {

  // Props

  protected $buffer = '';
  public $delim;
  public $bytes;

  // Methods

  public function __construct($delim = "\n") {
    $this->delim = $delim;
    $this->bytes = strlen($this->delim);
  }

  public function write($str) {
    $offset = 0;
    $index = 0;
    while(true) {
      if(!isset($str[$index])) break;
      if(substr($str, $index, $this->bytes) === $this->delim) {
        $index += $this->bytes;
        // echo "\nLINE ".json_encode($this->buffer.substr($str, $offset, $index-$offset))."\n";
        yield $this->buffer.substr($str, $offset, $index-$offset);
        $offset = $index;
        $this->buffer = '';
      } else {
        $index++;
      }
    }
    if($offset < $index) {
      $this->buffer = $this->buffer.substr($str, $offset, $index-$offset);
    }
  }

  public function end() {
    if($this->buffer !== '') {
      // echo "\n END ".json_encode($this->buffer)."\n";
      $str = $this->buffer;
      $this->buffer = '';
      foreach($this->write($str) as $part) {
        // echo "\n END LINE ".json_encode($part)."\n";
        yield $part;
      }
      if($this->buffer !== '') {
        // echo "\n BUFFER LINE ".json_encode($this->buffer)."\n";
        yield $this->buffer;
        $this->buffer = '';
      }
    }
  }

  public function bufferAppend($str) {
    $this->buffer = $this->buffer.$str;
  }

  public function bufferClear() {
    $this->buffer = '';
  }

  // Statics

  // static public function chars($str) {
  //   $index = 0;
  //   while(($char = self::nextchar($str, $index)) !== false) {
  //     yield $char;
  //   }
  // }
  //
  // static public function nextchar($str, &$pointer){
  //   if(!isset($str[$pointer])) return false;
  //
  //   $char = ord($str[$pointer]);
  //
  //   if($char < 128) {
  //     return $str[$pointer++];
  //   }
  //
  //   if($char < 224) {
  //     $bytes = 2;
  //   } else if($char < 240) {
  //     $bytes = 3;
  //   } else if($char < 248) {
  //     $bytes = 4;
  //   } else if($char == 252) {
  //     $bytes = 5;
  //   } else {
  //     $bytes = 6;
  //   }
  //
  //   $str = substr($str, $pointer, $bytes);
  //   $pointer += $bytes;
  //
  //   return $str;
  // }
}
