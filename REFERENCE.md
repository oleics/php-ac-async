
#### Functions

  * *void* **async**  ( *callable* $fn, $priority = 0, $args = null ) [#](src/Async.php#L8-L10)  

  * *void* **async_schedule**  ( *callable* $fn, $forFrame = 0, $args = null ) [#](src/Async.php#L13-L15)  

  * *void* **async_scheduleEach**  ( *callable* $fn, $eachFrame = 0, $args = null ) [#](src/Async.php#L18-L20)  

  * *void* **async_setTimeout**  ( *callable* $fn, $seconds = 0, $args = null ) [#](src/Async.php#L23-L25)  

  * *void* **async_setInterval**  ( *callable* $fn, $seconds = 0, $args = null ) [#](src/Async.php#L28-L30)  

  * *void* **async_removeFromSchedules**  ( *callable* $fn ) [#](src/Async.php#L33-L35)  

#### Classes

  * <a name="Ac_Async_Async"></a> *class* **Async** ( ) [#](src/Async.php#L45-L231)  

    * *Constants*  

      * *Async*::**STATE_KERNEL_STOPPED** = 1  

      * *Async*::**STATE_KERNEL_STOPPING** = 2  

      * *Async*::**STATE_KERNEL_EMPTY** = 3  

      * *Async*::**STATE_KERNEL_RUNNING** = 4  

      * *Async*::**STATE_ENGINE_STOPPED** = 5  

      * *Async*::**STATE_ENGINE_STOPPING** = 6  

      * *Async*::**STATE_ENGINE_EMPTY** = 7  

      * *Async*::**STATE_ENGINE_RUNNING** = 8  

    * *Static Methods*  

      * *Async*::**configure**  ( $engine_framerate = null, $kernel_framerate = null, $kernel_defaultToFileMode = false ) [#](src/Async.php#L69-L73)  

      * *Async*::**wrap**  ( $filename ) [#](src/Async.php#L77-L81)  

      * *Async*::**blockStart**  (  ) [#](src/Async.php#L85-L88)  

      * *Async*::**blockEnd**  (  ) [#](src/Async.php#L90-L95)  

      * *Async*::&**getEngine**  (  ) [#](src/Async.php#L219-L221)  

      * *Async*::&**getKernel**  (  ) [#](src/Async.php#L223-L225)  

      * *Async*::&**getSelect**  (  ) [#](src/Async.php#L227-L229)  

  * <a name="Ac_Async_Engine"></a> *class* **Engine** ( $framerate = null, *[Kernel](#Ac_Async_Kernel)* &$kernel = null ) [#](src/Engine.php#L8-L237)  

    * *Properties*  

      * *Engine*->**framerate** = 0.0167  

      * *Engine*->**frame** = 0  

    * *Methods*  

      * *Engine*->**__construct**  ( $framerate = null, *[Kernel](#Ac_Async_Kernel)* &$kernel = null ) [#](src/Engine.php#L21-L27)  

      * *Engine*->**getKernel**  (  ) [#](src/Engine.php#L31-L33)  

      * *Engine*->**setKernel**  ( *[Kernel](#Ac_Async_Kernel)* &$kernel ) [#](src/Engine.php#L35-L44)  

      * *Engine*->**changeFramerate**  ( $framerate ) [#](src/Engine.php#L48-L51)  

      * *Engine*->**start**  ( *callable* $onFrame = null ) [#](src/Engine.php#L55-L61)  

      * *Engine*->**stop**  (  ) [#](src/Engine.php#L63-L68)  

      * *Engine*->**isEmpty**  (  ) [#](src/Engine.php#L128-L133)  

      * *Engine*->**enqueue**  ( *callable* $fn, $args = null, $priority = 0 ) [#](src/Engine.php#L137-L150)  

      * *Engine*->**schedule**  ( *callable* $fn, $args = null, $forFrame = 0 ) [#](src/Engine.php#L154-L173)  

      * *Engine*->**scheduleEach**  ( *callable* $fn, $args = null, $eachFrame = 0 ) [#](src/Engine.php#L175-L190)  

      * *Engine*->**removeFromSchedules**  ( *callable* $fn ) [#](src/Engine.php#L192-L223)  

      * *Engine*->**setTimeout**  ( *callable* $fn, $args = null, $seconds = 0 ) [#](src/Engine.php#L227-L230)  

      * *Engine*->**setInterval**  ( *callable* $fn, $args = null, $seconds = 0 ) [#](src/Engine.php#L232-L235)  

  * <a name="Ac_Async_Json"></a> *abstract* *class* **Json** [#](src/Json.php#L9-L30)  

    * *Static Methods*  

      * *Json*::**decode**  ( $d ) [#](src/Json.php#L14-L20)  

      * *Json*::**encode**  ( $d, $pretty = false ) [#](src/Json.php#L22-L28)  

      * *Json*::**read**  ( $stream, *callable* $callback, *callable* $callbackData = null ) [#](src/Json/ReadTrait.php#L11-L28)  

      * *Json*::**write**  ( $stream ) [#](src/Json/WriteTrait.php#L10-L16)  

  * <a name="Ac_Async_Kernel"></a> *class* **Kernel** ( $framerate = null, *[Select](#Ac_Async_Select)* &$select = null, $defaultToFileMode = false ) [#](src/Kernel.php#L13-L225)  

    * *Constants*  

      * *Kernel*::**MODE_SOCKET** = "socket"  

      * *Kernel*::**MODE_FILE** = "file"  

      * *Kernel*::**READ_BYTES_MAX** = 8192  

    * *Properties*  

      * *Kernel*->**mode**  

      * *Kernel*->**isRunning** = false  

      * *Kernel*->**framerate** = 0.0167  

      * *Kernel*->**frame** = 0  

      * *Kernel*->**timeElapsedFrame** = 0  

      * *Kernel*->**timeElapsedTotal** = 0  

      * *Kernel*->**timeDrift** = 0  

      * *Kernel*->**successiveNegativeTimeDrifts** = 0  

    * *Methods*  

      * *Kernel*->**__construct**  ( $framerate = null, *[Select](#Ac_Async_Select)* &$select = null, $defaultToFileMode = false ) [#](src/Kernel.php#L43-L71)  

      * *Kernel*->**isEmpty**  (  ) [#](src/Kernel.php#L75-L81)  

      * *Kernel*->&**getSelect**  (  ) [#](src/Kernel.php#L83-L85)  

      * *Kernel*->**start**  ( *callable* $onFrame = null ) [#](src/Kernel.php#L121-L143)  

      * *Kernel*->**step**  (  ) [#](src/Kernel.php#L145-L148)  

      * *Kernel*->**stop**  (  ) [#](src/Kernel.php#L150-L154)  

      * *Kernel*->**addCallback**  ( *callable* $fn ) [#](src/Kernel.php#L212-L215)  

      * *Kernel*->**removeCallback**  ( *callable* $fn ) [#](src/Kernel.php#L217-L223)  

      * *Kernel*->**setLog**  ( $log ) [#](src/Kernel/LogTrait.php#L11-L13)  

      * *Kernel*->**frameInfos**  (  ) [#](src/Kernel/LogTrait.php#L15-L67)  

    * *Static Properties*  

      * *Kernel*::**$SUCCESSIVE_DRIFTS_WARN** = 20  

      * *Kernel*::**$SUCCESSIVE_DRIFTS_FATAL** = 200  

  * <a name="Ac_Async_Log"></a> *class* **Log** ( $stream = Ac\Async\STDOUT ) [#](src/Log.php#L9-L59)  

    * *Methods*  

      * *Log*->**__construct**  ( $stream = Ac\Async\STDOUT ) [#](src/Log.php#L14-L16)  

      * *Log*->**debug**  (  ) [#](src/Log.php#L29-L31)  

      * *Log*->**log**  (  ) [#](src/Log.php#L33-L35)  

      * *Log*->**info**  (  ) [#](src/Log.php#L37-L39)  

      * *Log*->**warn**  (  ) [#](src/Log.php#L41-L44)  

      * *Log*->**fatal**  (  ) [#](src/Log.php#L46-L52)  

      * *Log*->**beep**  (  ) [#](src/Log.php#L54-L57)  

  * <a name="Ac_Async_Process"></a> *class* **Process** ( $cmd ) [#](src/Process.php#L7-L141)  

    * *Constants*  

      * *Process*::**SIGNAL_SIGTERM** = 15  

    * *Properties*  

      * *Process*->**stdin**  

      * *Process*->**stdout**  

      * *Process*->**stderr**  

      * *Process*->**command**  

      * *Process*->**pid**  

      * *Process*->**running**  

      * *Process*->**signaled**  

      * *Process*->**stopped**  

      * *Process*->**exitcode**  

      * *Process*->**termsig**  

      * *Process*->**stopsig**  

    * *Methods*  

      * *Process*->**__construct**  ( $cmd ) [#](src/Process.php#L35-L43)  

      * *Process*->**kill**  ( $signal = self::SIGNAL_SIGTERM ) [#](src/Process.php#L71-L78)  

      * *Process*->**close**  (  ) [#](src/Process.php#L80-L93)  

      * *Process*->**read**  ( *callable* $callback ) [#](src/Process.php#L97-L118)  

      * *Process*->**readStdout**  ( *callable* $callback ) [#](src/Process.php#L120-L122)  

      * *Process*->**readStderr**  ( *callable* $callback ) [#](src/Process.php#L124-L126)  

      * *Process*->**write**  ( $data, *callable* $callback = null ) [#](src/Process.php#L128-L133)  

    * *Static Methods*  

      * *Process*::**spawn**  ( $cmd ) [#](src/Process.php#L137-L139)  

  * <a name="Ac_Async_Select"></a> *class* **Select** ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 ) [#](src/Select.php#L8-L404)  

    * *Constants*  

      * *Select*::**CHUNK_SIZE** = 8192  

      * *Select*::**READ_BUFFER_SIZE** = 0  

      * *Select*::**WRITE_BUFFER_SIZE** = 0  

      * *Select*::**IDLE** = 0  

      * *Select*::**ACTIVE** = 1  

      * *Select*::**DONE** = 3  

    * *Methods*  

      * *Select*->**__construct**  ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 ) [#](src/Select.php#L40-L47)  

      * *Select*->**select**  (  ) [#](src/Select.php#L60-L126)  

      * *Select*->**addIdleCallback**  ( *callable* $callback ) [#](src/Select.php#L165-L169)  

      * *Select*->**removeIdleCallback**  ( *callable* $callback ) [#](src/Select.php#L171-L176)  

      * *Select*->**addActiveCallback**  ( *callable* $callback ) [#](src/Select.php#L186-L190)  

      * *Select*->**removeActiveCallback**  ( *callable* $callback ) [#](src/Select.php#L192-L197)  

      * *Select*->**addDoneCallback**  ( *callable* $callback ) [#](src/Select.php#L207-L211)  

      * *Select*->**removeDoneCallback**  ( *callable* $callback ) [#](src/Select.php#L213-L218)  

      * *Select*->**addCallbackReadable**  ( *callable* $callback, $stream = null ) [#](src/Select.php#L228-L231)  

      * *Select*->**removeCallbackReadable**  ( *callable* $callback ) [#](src/Select.php#L233-L240)  

      * *Select*->**addCallbackWritable**  ( *callable* $callback, $stream = null ) [#](src/Select.php#L260-L263)  

      * *Select*->**removeCallbackWritable**  ( *callable* $callback ) [#](src/Select.php#L265-L272)  

      * *Select*->**addCallbackExceptable**  ( *callable* $callback, $stream = null ) [#](src/Select.php#L292-L295)  

      * *Select*->**removeCallbackExceptable**  ( *callable* $callback ) [#](src/Select.php#L297-L304)  

      * *Select*->**addReadable**  ( $stream ) [#](src/Select.php#L327-L334)  

      * *Select*->**removeReadable**  ( $stream ) [#](src/Select.php#L336-L341)  

      * *Select*->**numReadables**  (  ) [#](src/Select.php#L343-L345)  

      * *Select*->**addWritable**  ( $stream ) [#](src/Select.php#L349-L355)  

      * *Select*->**removeWritable**  ( $stream ) [#](src/Select.php#L357-L363)  

      * *int* *Select*->**numWritables**  (  ) [#](src/Select.php#L368-L370)  

      * *bool* *Select*->**addExceptable**  ( $stream ) [#](src/Select.php#L377-L384)  

      * *bool* *Select*->**removeExceptable**  ( $stream ) [#](src/Select.php#L389-L395)  

      * *int* *Select*->**numExceptables**  (  ) [#](src/Select.php#L400-L402)  

  * <a name="Ac_Async_Stream"></a> *abstract* *class* **Stream** [#](src/Stream.php#L10-L17)  

    * *Static Methods*  

      * *Stream*::**openProcess**  ( $cmd ) [#](src/Stream/CommonTrait.php#L7-L9)  

      * *Stream*::**openReadable**  ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L11-L13)  

      * *Stream*::**openWritable**  ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L15-L17)  

      * *Stream*::**openDuplex**  ( $url = "php://temp" ) [#](src/Stream/CommonTrait.php#L19-L21)  

      * *Stream*::**spawnProcess**  ( $cmd ) [#](src/Stream/ProcessTrait.php#L9-L11)  

      * *Stream*::**read**  ( $stream, *callable* $callback ) [#](src/Stream/ReadTrait.php#L11-L33)  

      * *Stream*::**readAndParse**  ( $stream, *[StringParser](#Ac_Async_StringParser)* $parser, *callable* $callback ) [#](src/Stream/ReadTrait.php#L35-L51)  

      * *Stream*::**readLines**  ( $stream, *callable* $callback ) [#](src/Stream/ReadTrait.php#L53-L56)  

      * *Stream*::**write**  ( $stream ) [#](src/Stream/WriteTrait.php#L11-L54)  

  * <a name="Ac_Async_StringParser"></a> *class* **StringParser** ( $delim = "\n" ) [#](src/StringParser.php#L5-L100)  

    * *Properties*  

      * *StringParser*->**delim**  

      * *StringParser*->**bytes**  

    * *Methods*  

      * *StringParser*->**__construct**  ( $delim = "\n" ) [#](src/StringParser.php#L15-L18)  

      * *StringParser*->**write**  ( $str ) [#](src/StringParser.php#L20-L38)  

      * *StringParser*->**end**  (  ) [#](src/StringParser.php#L40-L55)  

      * *StringParser*->**bufferAppend**  ( $str ) [#](src/StringParser.php#L57-L59)  

      * *StringParser*->**bufferClear**  (  ) [#](src/StringParser.php#L61-L63)  
