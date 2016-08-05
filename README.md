
ac-async
========

Dependency-free, tick-based event-loop for non-blocking I/O and realtime simulations with PHP.

No worries! No need to learn a whole new framework, ``ac-async`` is here to give you only the necessary interface in a convenient way to write asynchronous functions and utilize non-blocking I/O in PHP.

Features / Goals
----------------

  * Runs everywhere (PHP >= 5.3)
  * Time-synced frames
  * Non-blocking I/O
  * Asynchronous callable-execution
    * Prioritized callable-execution
    * Scheduled callable-execution
    * Repeated scheduled callable-execution
    * Timeouts
    * Intervals
  * Convenient API
  * Plays well with traditional web-servers like Apache
  * Tries to be as unobtrusive as possible
  * Does not force "all-new-n-fancy style of writing code" on you
  * Though, eases functional programming style in PHP
    * Keeps the size of the call-stack low (eg. for tail-recursive functions)
    * Low memory-footprint for I/O heavy routines
  * Again, no need to learn whole new framework, ``ac-async`` provides only the necessary interface in a convenient way to write asynchronous functions and utilize non-blocking I/O in PHP

Install
-------

#### Download

async.phar

#### Script

cURL:

```sh
curl -o- https://raw.githubusercontent.com/oleics/ac-async/master/install.php | sudo php
```

Wget:

```sh
wget -qO-  https://raw.githubusercontent.com/oleics/ac-async/master/install.php | sudo php
```

#### Composer

```sh
composer require oleics/ac-async
```

Usage Examples
--------------

```sh
php async.phar examples/simple.php
```

See [examples-folder](examples) for more.

Tests
-----

If you have installed [ac-testa](https://github.com/oleics/php-ac-testa):

```sh
testa
```

Or you can execute the test this way:

```sh
php vendor/oleics/ac-testa/testa.phar
```

API
---

[REFERENCE.md](REFERENCE.md)

MIT License
-----------

Copyright (c) 2016 Oliver Leics <oliver.leics@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
