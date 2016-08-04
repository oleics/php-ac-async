
#### Functions

  * *void* **[async](src/Async.php#L8-L10)**  ( *callable* $fn, $priority = 0, $args = null )  

  * *void* **[async_schedule](src/Async.php#L13-L15)**  ( *callable* $fn, $forFrame = 0, $args = null )  

  * *void* **[async_scheduleEach](src/Async.php#L18-L20)**  ( *callable* $fn, $eachFrame = 0, $args = null )  

  * *void* **[async_setTimeout](src/Async.php#L23-L25)**  ( *callable* $fn, $seconds = 0, $args = null )  

  * *void* **[async_setInterval](src/Async.php#L28-L30)**  ( *callable* $fn, $seconds = 0, $args = null )  

  * *void* **[async_removeFromSchedules](src/Async.php#L33-L35)**  ( *callable* $fn )  

#### Classes

  * <a name="Ac_Async_Async"></a> *class* **[Async](src/Async.php#L45-L231)** ( )  

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

      * *Async*::**[configure](src/Async.php#L69-L73)**  ( $engine_framerate = null, $kernel_framerate = null, $kernel_defaultToFileMode = false )  

      * *Async*::**[wrap](src/Async.php#L77-L81)**  ( $filename )  

      * *Async*::**[blockStart](src/Async.php#L85-L88)**  (  )  

      * *Async*::**[blockEnd](src/Async.php#L90-L95)**  (  )  

      * *Async*::&**[getEngine](src/Async.php#L219-L221)**  (  )  

      * *Async*::&**[getKernel](src/Async.php#L223-L225)**  (  )  

      * *Async*::&**[getSelect](src/Async.php#L227-L229)**  (  )  

  * <a name="Ac_Async_Engine"></a> *class* **[Engine](src/Engine.php#L8-L237)** ( $framerate = null, *[Kernel](#Ac_Async_Kernel)* &$kernel = null )  

    * *Properties*  

      * *Engine*->**framerate** = 0.0167  

      * *Engine*->**frame** = 0  

    * *Methods*  

      * *Engine*->**[__construct](src/Engine.php#L21-L27)**  ( $framerate = null, *[Kernel](#Ac_Async_Kernel)* &$kernel = null )  

      * *Engine*->**[getKernel](src/Engine.php#L31-L33)**  (  )  

      * *Engine*->**[setKernel](src/Engine.php#L35-L44)**  ( *[Kernel](#Ac_Async_Kernel)* &$kernel )  

      * *Engine*->**[changeFramerate](src/Engine.php#L48-L51)**  ( $framerate )  

      * *Engine*->**[start](src/Engine.php#L55-L61)**  ( *callable* $onFrame = null )  

      * *Engine*->**[stop](src/Engine.php#L63-L68)**  (  )  

      * *Engine*->**[isEmpty](src/Engine.php#L128-L133)**  (  )  

      * *Engine*->**[enqueue](src/Engine.php#L137-L150)**  ( *callable* $fn, $args = null, $priority = 0 )  

      * *Engine*->**[schedule](src/Engine.php#L154-L173)**  ( *callable* $fn, $args = null, $forFrame = 0 )  

      * *Engine*->**[scheduleEach](src/Engine.php#L175-L190)**  ( *callable* $fn, $args = null, $eachFrame = 0 )  

      * *Engine*->**[removeFromSchedules](src/Engine.php#L192-L223)**  ( *callable* $fn )  

      * *Engine*->**[setTimeout](src/Engine.php#L227-L230)**  ( *callable* $fn, $args = null, $seconds = 0 )  

      * *Engine*->**[setInterval](src/Engine.php#L232-L235)**  ( *callable* $fn, $args = null, $seconds = 0 )  

  * <a name="Ac_Async_Json"></a> *abstract* *class* **[Json](src/Json.php#L9-L30)**  

    * *Static Methods*  

      * *Json*::**[decode](src/Json.php#L14-L20)**  ( $d )  

      * *Json*::**[encode](src/Json.php#L22-L28)**  ( $d, $pretty = false )  

      * *Json*::**[read](src/Json/ReadTrait.php#L11-L28)**  ( $stream, *callable* $callback, *callable* $callbackData = null )  

      * *Json*::**[write](src/Json/WriteTrait.php#L10-L16)**  ( $stream )  

  * <a name="Ac_Async_Kernel"></a> *class* **[Kernel](src/Kernel.php#L13-L225)** ( $framerate = null, *[Select](#Ac_Async_Select)* &$select = null, $defaultToFileMode = false )  

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

      * *Kernel*->**[__construct](src/Kernel.php#L43-L71)**  ( $framerate = null, *[Select](#Ac_Async_Select)* &$select = null, $defaultToFileMode = false )  

      * *Kernel*->**[isEmpty](src/Kernel.php#L75-L81)**  (  )  

      * *Kernel*->&**[getSelect](src/Kernel.php#L83-L85)**  (  )  

      * *Kernel*->**[start](src/Kernel.php#L121-L143)**  ( *callable* $onFrame = null )  

      * *Kernel*->**[step](src/Kernel.php#L145-L148)**  (  )  

      * *Kernel*->**[stop](src/Kernel.php#L150-L154)**  (  )  

      * *Kernel*->**[addCallback](src/Kernel.php#L212-L215)**  ( *callable* $fn )  

      * *Kernel*->**[removeCallback](src/Kernel.php#L217-L223)**  ( *callable* $fn )  

      * *Kernel*->**[setLog](src/Kernel/LogTrait.php#L11-L13)**  ( $log )  

      * *Kernel*->**[frameInfos](src/Kernel/LogTrait.php#L15-L67)**  (  )  

    * *Static Properties*  

      * *Kernel*::**$SUCCESSIVE_DRIFTS_WARN** = 20  

      * *Kernel*::**$SUCCESSIVE_DRIFTS_FATAL** = 200  

  * <a name="Ac_Async_Log"></a> *class* **[Log](src/Log.php#L9-L59)** ( $stream = Ac\Async\STDOUT )  

    * *Methods*  

      * *Log*->**[__construct](src/Log.php#L14-L16)**  ( $stream = Ac\Async\STDOUT )  

      * *Log*->**[debug](src/Log.php#L29-L31)**  (  )  

      * *Log*->**[log](src/Log.php#L33-L35)**  (  )  

      * *Log*->**[info](src/Log.php#L37-L39)**  (  )  

      * *Log*->**[warn](src/Log.php#L41-L44)**  (  )  

      * *Log*->**[fatal](src/Log.php#L46-L52)**  (  )  

      * *Log*->**[beep](src/Log.php#L54-L57)**  (  )  

  * <a name="Ac_Async_Process"></a> *class* **[Process](src/Process.php#L7-L141)** ( $cmd )  

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

      * *Process*->**[__construct](src/Process.php#L35-L43)**  ( $cmd )  

      * *Process*->**[kill](src/Process.php#L71-L78)**  ( $signal = self::SIGNAL_SIGTERM )  

      * *Process*->**[close](src/Process.php#L80-L93)**  (  )  

      * *Process*->**[read](src/Process.php#L97-L118)**  ( *callable* $callback )  

      * *Process*->**[readStdout](src/Process.php#L120-L122)**  ( *callable* $callback )  

      * *Process*->**[readStderr](src/Process.php#L124-L126)**  ( *callable* $callback )  

      * *Process*->**[write](src/Process.php#L128-L133)**  ( $data, *callable* $callback = null )  

    * *Static Methods*  

      * *Process*::**[spawn](src/Process.php#L137-L139)**  ( $cmd )  

  * <a name="Ac_Async_Select"></a> *class* **[Select](src/Select.php#L8-L404)** ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 )  

    * *Constants*  

      * *Select*::**CHUNK_SIZE** = 8192  

      * *Select*::**READ_BUFFER_SIZE** = 0  

      * *Select*::**WRITE_BUFFER_SIZE** = 0  

      * *Select*::**IDLE** = 0  

      * *Select*::**ACTIVE** = 1  

      * *Select*::**DONE** = 3  

    * *Methods*  

      * *Select*->**[__construct](src/Select.php#L40-L47)**  ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 )  

      * *Select*->**[select](src/Select.php#L60-L126)**  (  )  

      * *Select*->**[addIdleCallback](src/Select.php#L165-L169)**  ( *callable* $callback )  

      * *Select*->**[removeIdleCallback](src/Select.php#L171-L176)**  ( *callable* $callback )  

      * *Select*->**[addActiveCallback](src/Select.php#L186-L190)**  ( *callable* $callback )  

      * *Select*->**[removeActiveCallback](src/Select.php#L192-L197)**  ( *callable* $callback )  

      * *Select*->**[addDoneCallback](src/Select.php#L207-L211)**  ( *callable* $callback )  

      * *Select*->**[removeDoneCallback](src/Select.php#L213-L218)**  ( *callable* $callback )  

      * *Select*->**[addCallbackReadable](src/Select.php#L228-L231)**  ( *callable* $callback, $stream = null )  

      * *Select*->**[removeCallbackReadable](src/Select.php#L233-L240)**  ( *callable* $callback )  

      * *Select*->**[addCallbackWritable](src/Select.php#L260-L263)**  ( *callable* $callback, $stream = null )  

      * *Select*->**[removeCallbackWritable](src/Select.php#L265-L272)**  ( *callable* $callback )  

      * *Select*->**[addCallbackExceptable](src/Select.php#L292-L295)**  ( *callable* $callback, $stream = null )  

      * *Select*->**[removeCallbackExceptable](src/Select.php#L297-L304)**  ( *callable* $callback )  

      * *Select*->**[addReadable](src/Select.php#L327-L334)**  ( $stream )  

      * *Select*->**[removeReadable](src/Select.php#L336-L341)**  ( $stream )  

      * *Select*->**[numReadables](src/Select.php#L343-L345)**  (  )  

      * *Select*->**[addWritable](src/Select.php#L349-L355)**  ( $stream )  

      * *Select*->**[removeWritable](src/Select.php#L357-L363)**  ( $stream )  

      * *int* *Select*->**[numWritables](src/Select.php#L368-L370)**  (  )  

      * *bool* *Select*->**[addExceptable](src/Select.php#L377-L384)**  ( $stream )  

      * *bool* *Select*->**[removeExceptable](src/Select.php#L389-L395)**  ( $stream )  

      * *int* *Select*->**[numExceptables](src/Select.php#L400-L402)**  (  )  

  * <a name="Ac_Async_Stream"></a> *abstract* *class* **[Stream](src/Stream.php#L10-L17)**  

    * *Static Methods*  

      * *Stream*::**[openProcess](src/Stream/CommonTrait.php#L7-L9)**  ( $cmd )  

      * *Stream*::**[openReadable](src/Stream/CommonTrait.php#L11-L13)**  ( $url = "php://temp" )  

      * *Stream*::**[openWritable](src/Stream/CommonTrait.php#L15-L17)**  ( $url = "php://temp" )  

      * *Stream*::**[openDuplex](src/Stream/CommonTrait.php#L19-L21)**  ( $url = "php://temp" )  

      * *Stream*::**[spawnProcess](src/Stream/ProcessTrait.php#L9-L11)**  ( $cmd )  

      * *Stream*::**[read](src/Stream/ReadTrait.php#L11-L33)**  ( $stream, *callable* $callback )  

      * *Stream*::**[readAndParse](src/Stream/ReadTrait.php#L35-L51)**  ( $stream, *[StringParser](#Ac_Async_StringParser)* $parser, *callable* $callback )  

      * *Stream*::**[readLines](src/Stream/ReadTrait.php#L53-L56)**  ( $stream, *callable* $callback )  

      * *Stream*::**[write](src/Stream/WriteTrait.php#L11-L54)**  ( $stream )  

  * <a name="Ac_Async_StringParser"></a> *class* **[StringParser](src/StringParser.php#L5-L100)** ( $delim = "\n" )  

    * *Properties*  

      * *StringParser*->**delim**  

      * *StringParser*->**bytes**  

    * *Methods*  

      * *StringParser*->**[__construct](src/StringParser.php#L15-L18)**  ( $delim = "\n" )  

      * *StringParser*->**[write](src/StringParser.php#L20-L38)**  ( $str )  

      * *StringParser*->**[end](src/StringParser.php#L40-L55)**  (  )  

      * *StringParser*->**[bufferAppend](src/StringParser.php#L57-L59)**  ( $str )  

      * *StringParser*->**[bufferClear](src/StringParser.php#L61-L63)**  (  )  
