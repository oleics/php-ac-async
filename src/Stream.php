<?php

namespace Ac\Async;

use Ac\Async\Stream\CommonTrait;
use Ac\Async\Stream\ProcessTrait;
use Ac\Async\Stream\ReadTrait;
use Ac\Async\Stream\WriteTrait;

abstract class Stream {

  use CommonTrait;
  use ProcessTrait;
  use ReadTrait;
  use WriteTrait;

}
