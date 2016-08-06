
ac-async

# Reference 

## Global  

### *Functions (5)*

  * *void* **async** ( *callable* $fn, *integer* $priority = 0, *array* $args = null ) [#](src/Async.php#L15-L17)  
    Execute callable `$fn`.  

  * *void* **async_schedule** ( *callable* $fn, *integer* $forFrame = 0, *array* $args = null ) [#](src/Async.php#L25-L27)  

  * *void* **async_scheduleEach** ( *callable* $fn, *integer* $eachFrame = 0, *array* $args = null ) [#](src/Async.php#L35-L37)  

  * *void* **async_setTimeout** ( *callable* $fn, *real* $seconds = 0.0, *array* $args = null ) [#](src/Async.php#L45-L47)  

  * *void* **async_setInterval** ( *callable* $fn, *real* $seconds = 0.0, *array* $args = null ) [#](src/Async.php#L55-L57)  


## Ac\Async  

### *Classes (9)*

  * <a name="Ac_Async_Async"></a> *abstract* *class* **Async** [#](src/Async.php#L75-L272)  

    * *Constants (8)*  

      * *integer* **STATE_KERNEL_STOPPED** = 1  
      * *integer* **STATE_KERNEL_STOPPING** = 2  
      * *integer* **STATE_KERNEL_EMPTY** = 3  
      * *integer* **STATE_KERNEL_RUNNING** = 4  
      * *integer* **STATE_ENGINE_STOPPED** = 5  
      * *integer* **STATE_ENGINE_STOPPING** = 6  
      * *integer* **STATE_ENGINE_EMPTY** = 7  
      * *integer* **STATE_ENGINE_RUNNING** = 8  

    * *Static Methods (7)*  

      * *void* _Async::_**$configure** ( *real|null* $engine_framerate = null, *real|null* $kernel_framerate = null, *boolean* $kernel_defaultToFileMode = false ) [#](src/Async.php#L106-L110)  

      * *void* _Async::_**$wrap** ( $filename ) [#](src/Async.php#L118-L122)  
          Wrap a file.  

      * _Async::_**$blockStart** (  ) [#](src/Async.php#L126-L129)  

      * _Async::_**$blockEnd** (  ) [#](src/Async.php#L131-L136)  

      * _Async::_**$getEngine** (  ) [#](src/Async.php#L260-L262)  

      * _Async::_**$getKernel** (  ) [#](src/Async.php#L264-L266)  

      * _Async::_**$getSelect** (  ) [#](src/Async.php#L268-L270)  

  * <a name="Ac_Async_Engine"></a> *class* **Engine** [#](src/Engine.php#L8-L245)  

    * *Properties (2)*  

      * *real* _Engine->_**framerate** = 0.0167  

      * *real* _Engine->_**frame** = 0  

    * *Methods (13)*  

      * _Engine->_**__construct** ( $framerate = null, *<a href="#Ac_Async_Kernel">Kernel</a>* &$kernel = null ) [#](src/Engine.php#L29-L35)  

      * _Engine->_**getKernel** (  ) [#](src/Engine.php#L39-L41)  

      * _Engine->_**setKernel** ( *<a href="#Ac_Async_Kernel">Kernel</a>* &$kernel ) [#](src/Engine.php#L43-L52)  

      * _Engine->_**changeFramerate** ( $framerate ) [#](src/Engine.php#L56-L59)  

      * _Engine->_**start** ( *callable* $onFrame = null ) [#](src/Engine.php#L63-L69)  

      * _Engine->_**stop** (  ) [#](src/Engine.php#L71-L76)  

      * _Engine->_**isEmpty** (  ) [#](src/Engine.php#L136-L141)  

      * _Engine->_**enqueue** ( *callable* $fn, $args = null, $priority = 0 ) [#](src/Engine.php#L145-L158)  

      * _Engine->_**schedule** ( *callable* $fn, $args = null, $forFrame = 0 ) [#](src/Engine.php#L162-L181)  

      * _Engine->_**scheduleEach** ( *callable* $fn, $args = null, $eachFrame = 0 ) [#](src/Engine.php#L183-L198)  

      * _Engine->_**removeFromSchedules** ( *callable* $fn ) [#](src/Engine.php#L200-L231)  

      * _Engine->_**setTimeout** ( *callable* $fn, $args = null, $seconds = 0.0 ) [#](src/Engine.php#L235-L238)  

      * _Engine->_**setInterval** ( *callable* $fn, $args = null, $seconds = 0.0 ) [#](src/Engine.php#L240-L243)  

  * <a name="Ac_Async_Json"></a> *abstract* *class* **Json** [#](src/Json.php#L9-L30)  

    * *Static Methods (4)*  

      * _Json::_**$decode** ( $d ) [#](src/Json.php#L14-L20)  

      * _Json::_**$encode** ( $d, $pretty = false ) [#](src/Json.php#L22-L28)  

      * _Json::_**$read** ( $stream, *callable* $callback, *callable* $callbackData = null ) [#](src/Json/ReadTrait.php#L11-L28)  

      * _Json::_**$write** ( $stream ) [#](src/Json/WriteTrait.php#L10-L16)  

  * <a name="Ac_Async_Kernel"></a> *class* **Kernel** [#](src/Kernel.php#L16-L228)  
    The kernel.  

    * *Constants (3)*  

      * *string* **MODE_SOCKET** = socket  
      * *string* **MODE_FILE** = file  
      * *integer* **READ_BYTES_MAX** = 8192  

    * *Properties (8)*  

      * _Kernel->_**mode**  

      * _Kernel->_**isRunning** = false  

      * _Kernel->_**framerate** = 0.0167  

      * _Kernel->_**frame** = 0  

      * _Kernel->_**timeElapsedFrame** = 0.0  

      * _Kernel->_**timeElapsedTotal** = 0.0  

      * _Kernel->_**timeDrift** = 0.0  

      * _Kernel->_**successiveNegativeTimeDrifts** = 0  

    * *Methods (10)*  

      * _Kernel->_**__construct** ( $framerate = null, *<a href="#Ac_Async_Select">Select</a>* &$select = null, $defaultToFileMode = false ) [#](src/Kernel.php#L46-L74)  

      * _Kernel->_**isEmpty** (  ) [#](src/Kernel.php#L78-L84)  

      * _Kernel->_**getSelect** (  ) [#](src/Kernel.php#L86-L88)  

      * _Kernel->_**start** ( *callable* $onFrame = null ) [#](src/Kernel.php#L124-L146)  

      * _Kernel->_**step** (  ) [#](src/Kernel.php#L148-L151)  

      * _Kernel->_**stop** (  ) [#](src/Kernel.php#L153-L157)  

      * _Kernel->_**addCallback** ( *callable* $fn ) [#](src/Kernel.php#L215-L218)  

      * _Kernel->_**removeCallback** ( *callable* $fn ) [#](src/Kernel.php#L220-L226)  

      * _Kernel->_**setLog** ( $log ) [#](src/Kernel/LogTrait.php#L11-L13)  

      * _Kernel->_**frameInfos** (  ) [#](src/Kernel/LogTrait.php#L15-L67)  

    * *Static Properties (2)*  

      * _Kernel::_**$SUCCESSIVE_DRIFTS_WARN** = 20  

      * _Kernel::_**$SUCCESSIVE_DRIFTS_FATAL** = 200  

  * <a name="Ac_Async_Log"></a> *class* **Log** [#](src/Log.php#L9-L59)  

    * *Methods (7)*  

      * _Log->_**__construct** ( $stream = Ac\Async\STDOUT ) [#](src/Log.php#L14-L16)  

      * _Log->_**debug** (  ) [#](src/Log.php#L29-L31)  

      * _Log->_**log** (  ) [#](src/Log.php#L33-L35)  

      * _Log->_**info** (  ) [#](src/Log.php#L37-L39)  

      * _Log->_**warn** (  ) [#](src/Log.php#L41-L44)  

      * _Log->_**fatal** (  ) [#](src/Log.php#L46-L52)  

      * _Log->_**beep** (  ) [#](src/Log.php#L54-L57)  

  * <a name="Ac_Async_Process"></a> *class* **Process** [#](src/Process.php#L7-L141)  

    * *Constants (1)*  

      * *integer* **SIGNAL_SIGTERM** = 15  

    * *Properties (11)*  

      * _Process->_**stdin**  

      * _Process->_**stdout**  

      * _Process->_**stderr**  

      * _Process->_**command**  

      * _Process->_**pid**  

      * _Process->_**running**  

      * _Process->_**signaled**  

      * _Process->_**stopped**  

      * _Process->_**exitcode**  

      * _Process->_**termsig**  

      * _Process->_**stopsig**  

    * *Methods (7)*  

      * _Process->_**__construct** ( $cmd ) [#](src/Process.php#L35-L43)  

      * _Process->_**kill** ( $signal = self::SIGNAL_SIGTERM ) [#](src/Process.php#L71-L78)  

      * _Process->_**close** (  ) [#](src/Process.php#L80-L93)  

      * _Process->_**read** ( *callable* $callback ) [#](src/Process.php#L97-L118)  

      * _Process->_**readStdout** ( *callable* $callback ) [#](src/Process.php#L120-L122)  

      * _Process->_**readStderr** ( *callable* $callback ) [#](src/Process.php#L124-L126)  

      * _Process->_**write** ( $data, *callable* $callback = null ) [#](src/Process.php#L128-L133)  

    * *Static Methods (1)*  

      * _Process::_**$spawn** ( $cmd ) [#](src/Process.php#L137-L139)  

  * <a name="Ac_Async_Select"></a> *class* **Select** [#](src/Select.php#L8-L404)  

    * *Constants (6)*  

      * *integer* **CHUNK_SIZE** = 8192  
      * *integer* **READ_BUFFER_SIZE** = 0  
      * *integer* **WRITE_BUFFER_SIZE** = 0  
      * *integer* **IDLE** = 0  
      * *integer* **ACTIVE** = 1  
      * *integer* **DONE** = 3  

    * *Methods (23)*  

      * _Select->_**__construct** ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 ) [#](src/Select.php#L40-L47)  

      * _Select->_**select** (  ) [#](src/Select.php#L60-L126)  

      * _Select->_**addIdleCallback** ( *callable* $callback ) [#](src/Select.php#L165-L169)  

      * _Select->_**removeIdleCallback** ( *callable* $callback ) [#](src/Select.php#L171-L176)  

      * _Select->_**addActiveCallback** ( *callable* $callback ) [#](src/Select.php#L186-L190)  

      * _Select->_**removeActiveCallback** ( *callable* $callback ) [#](src/Select.php#L192-L197)  

      * _Select->_**addDoneCallback** ( *callable* $callback ) [#](src/Select.php#L207-L211)  

      * _Select->_**removeDoneCallback** ( *callable* $callback ) [#](src/Select.php#L213-L218)  

      * _Select->_**addCallbackReadable** ( *callable* $callback, $stream = null ) [#](src/Select.php#L228-L231)  

      * _Select->_**removeCallbackReadable** ( *callable* $callback ) [#](src/Select.php#L233-L240)  

      * _Select->_**addCallbackWritable** ( *callable* $callback, $stream = null ) [#](src/Select.php#L260-L263)  

      * _Select->_**removeCallbackWritable** ( *callable* $callback ) [#](src/Select.php#L265-L272)  

      * _Select->_**addCallbackExceptable** ( *callable* $callback, $stream = null ) [#](src/Select.php#L292-L295)  

      * _Select->_**removeCallbackExceptable** ( *callable* $callback ) [#](src/Select.php#L297-L304)  

      * _Select->_**addReadable** ( $stream ) [#](src/Select.php#L327-L334)  

      * _Select->_**removeReadable** ( $stream ) [#](src/Select.php#L336-L341)  

      * _Select->_**numReadables** (  ) [#](src/Select.php#L343-L345)  

      * _Select->_**addWritable** ( $stream ) [#](src/Select.php#L349-L355)  

      * _Select->_**removeWritable** ( $stream ) [#](src/Select.php#L357-L363)  

      * *integer* _Select->_**numWritables** (  ) [#](src/Select.php#L368-L370)  

      * *boolean* _Select->_**addExceptable** ( $stream ) [#](src/Select.php#L377-L384)  

      * *boolean* _Select->_**removeExceptable** ( $stream ) [#](src/Select.php#L389-L395)  

      * *integer* _Select->_**numExceptables** (  ) [#](src/Select.php#L400-L402)  

  * <a name="Ac_Async_Stream"></a> *abstract* *class* **Stream** [#](src/Stream.php#L10-L17)  

    * *Static Methods (9)*  

      * _Stream::_**$openProcess** ( $cmd ) [#](src/Stream/CommonTrait.php#L7-L9)  

      * _Stream::_**$openReadable** ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L11-L13)  

      * _Stream::_**$openWritable** ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L15-L17)  

      * _Stream::_**$openDuplex** ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L19-L21)  

      * _Stream::_**$spawnProcess** ( $cmd ) [#](src/Stream/ProcessTrait.php#L9-L11)  

      * _Stream::_**$read** ( $stream, *callable* $callback ) [#](src/Stream/ReadTrait.php#L11-L33)  

      * _Stream::_**$readAndParse** ( $stream, *<a href="#Ac_Async_StringParser">StringParser</a>* $parser, *callable* $callback ) [#](src/Stream/ReadTrait.php#L35-L51)  

      * _Stream::_**$readLines** ( $stream, *callable* $callback ) [#](src/Stream/ReadTrait.php#L53-L56)  

      * _Stream::_**$write** ( $stream ) [#](src/Stream/WriteTrait.php#L11-L54)  

  * <a name="Ac_Async_StringParser"></a> *class* **StringParser** [#](src/StringParser.php#L5-L100)  

    * *Properties (2)*  

      * _StringParser->_**delim**  

      * _StringParser->_**bytes**  

    * *Methods (5)*  

      * _StringParser->_**__construct** ( $delim = "\n" ) [#](src/StringParser.php#L15-L18)  

      * _StringParser->_**write** ( $str ) [#](src/StringParser.php#L20-L38)  

      * _StringParser->_**end** (  ) [#](src/StringParser.php#L40-L55)  

      * _StringParser->_**bufferAppend** ( $str ) [#](src/StringParser.php#L57-L59)  

      * _StringParser->_**bufferClear** (  ) [#](src/StringParser.php#L61-L63)  


## Ac\Async\Json  

### *Classes (2)*

  * <a name="Ac_Async_Json_ReadTrait"></a> *abstract* *trait* **ReadTrait** [#](src/Json/ReadTrait.php#L9-L30)  

    * *Static Methods (1)*  

      * _ReadTrait::_**$read** ( $stream, *callable* $callback, *callable* $callbackData = null ) [#](src/Json/ReadTrait.php#L11-L28)  

  * <a name="Ac_Async_Json_WriteTrait"></a> *abstract* *trait* **WriteTrait** [#](src/Json/WriteTrait.php#L8-L18)  

    * *Static Methods (1)*  

      * _WriteTrait::_**$write** ( $stream ) [#](src/Json/WriteTrait.php#L10-L16)  


## Ac\Async\Kernel  

### *Classes (1)*

  * <a name="Ac_Async_Kernel_LogTrait"></a> *abstract* *trait* **LogTrait** [#](src/Kernel/LogTrait.php#L7-L69)  

    * *Methods (2)*  

      * _LogTrait->_**setLog** ( $log ) [#](src/Kernel/LogTrait.php#L11-L13)  

      * _LogTrait->_**frameInfos** (  ) [#](src/Kernel/LogTrait.php#L15-L67)  

    * *Static Properties (2)*  

      * _LogTrait::_**$SUCCESSIVE_DRIFTS_WARN** = 20  

      * _LogTrait::_**$SUCCESSIVE_DRIFTS_FATAL** = 200  


## Ac\Async\Stream  

### *Classes (4)*

  * <a name="Ac_Async_Stream_CommonTrait"></a> *abstract* *trait* **CommonTrait** [#](src/Stream/CommonTrait.php#L5-L23)  

    * *Static Methods (4)*  

      * _CommonTrait::_**$openProcess** ( $cmd ) [#](src/Stream/CommonTrait.php#L7-L9)  

      * _CommonTrait::_**$openReadable** ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L11-L13)  

      * _CommonTrait::_**$openWritable** ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L15-L17)  

      * _CommonTrait::_**$openDuplex** ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L19-L21)  

  * <a name="Ac_Async_Stream_ProcessTrait"></a> *abstract* *trait* **ProcessTrait** [#](src/Stream/ProcessTrait.php#L7-L13)  

    * *Static Methods (1)*  

      * _ProcessTrait::_**$spawnProcess** ( $cmd ) [#](src/Stream/ProcessTrait.php#L9-L11)  

  * <a name="Ac_Async_Stream_ReadTrait"></a> *abstract* *trait* **ReadTrait** [#](src/Stream/ReadTrait.php#L9-L58)  

    * *Static Methods (3)*  

      * _ReadTrait::_**$read** ( $stream, *callable* $callback ) [#](src/Stream/ReadTrait.php#L11-L33)  

      * _ReadTrait::_**$readAndParse** ( $stream, *<a href="#Ac_Async_StringParser">StringParser</a>* $parser, *callable* $callback ) [#](src/Stream/ReadTrait.php#L35-L51)  

      * _ReadTrait::_**$readLines** ( $stream, *callable* $callback ) [#](src/Stream/ReadTrait.php#L53-L56)  

  * <a name="Ac_Async_Stream_WriteTrait"></a> *abstract* *trait* **WriteTrait** [#](src/Stream/WriteTrait.php#L9-L56)  

    * *Static Methods (1)*  

      * _WriteTrait::_**$write** ( $stream ) [#](src/Stream/WriteTrait.php#L11-L54)  

