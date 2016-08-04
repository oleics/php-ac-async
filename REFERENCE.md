
  * <a name="Ac_Async_Async"></a> *class* **Async** ( )  
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
      * *Async*::**configure**  ( $engine_framerate = null, $kernel_framerate = null, $kernel_defaultToFileMode = false )  
      * *Async*::**wrap**  ( $filename )  
      * *Async*::**blockStart**  (  )  
      * *Async*::**blockEnd**  (  )  
      * *Async*::&**getEngine**  (  )  
      * *Async*::&**getKernel**  (  )  
      * *Async*::&**getSelect**  (  )  
  * <a name="Ac_Async_Engine"></a> *class* **Engine** ( $framerate = null, *[Kernel](#Ac_Async_Kernel)* &$kernel = null )  
    * *Properties*  
      * *Engine*->**framerate** = 0.0167  
      * *Engine*->**frame** = 0  
    * *Methods*  
      * *Engine*->**__construct**  ( $framerate = null, *[Kernel](#Ac_Async_Kernel)* &$kernel = null )  
      * *Engine*->**getKernel**  (  )  
      * *Engine*->**setKernel**  ( *[Kernel](#Ac_Async_Kernel)* &$kernel )  
      * *Engine*->**changeFramerate**  ( $framerate )  
      * *Engine*->**start**  ( *callable* $onFrame = null )  
      * *Engine*->**stop**  (  )  
      * *Engine*->**isEmpty**  (  )  
      * *Engine*->**enqueue**  ( *callable* $fn, $args = null, $priority = 0 )  
      * *Engine*->**schedule**  ( *callable* $fn, $args = null, $forFrame = 0 )  
      * *Engine*->**scheduleEach**  ( *callable* $fn, $args = null, $eachFrame = 0 )  
      * *Engine*->**removeFromSchedules**  ( *callable* $fn )  
      * *Engine*->**setTimeout**  ( *callable* $fn, $args = null, $seconds = 0 )  
      * *Engine*->**setInterval**  ( *callable* $fn, $args = null, $seconds = 0 )  
  * <a name="Ac_Async_Json"></a> *abstract* *class* **Json**  
    * *Static Methods*  
      * *Json*::**decode**  ( $d )  
      * *Json*::**encode**  ( $d, $pretty = false )  
      * *Json*::**read**  ( $stream, *callable* $callback, *callable* $callbackData = null )  
      * *Json*::**write**  ( $stream )  
  * <a name="Ac_Async_Kernel"></a> *class* **Kernel** ( $framerate = null, *[Select](#Ac_Async_Select)* &$select = null, $defaultToFileMode = false )  
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
      * *Kernel*->**__construct**  ( $framerate = null, *[Select](#Ac_Async_Select)* &$select = null, $defaultToFileMode = false )  
      * *Kernel*->**isEmpty**  (  )  
      * *Kernel*->&**getSelect**  (  )  
      * *Kernel*->**start**  ( *callable* $onFrame = null )  
      * *Kernel*->**step**  (  )  
      * *Kernel*->**stop**  (  )  
      * *Kernel*->**addCallback**  ( *callable* $fn )  
      * *Kernel*->**removeCallback**  ( *callable* $fn )  
      * *Kernel*->**setLog**  ( $log )  
      * *Kernel*->**frameInfos**  (  )  
    * *Static Properties*  
      * *Kernel*::**$SUCCESSIVE_DRIFTS_WARN** = 20  
      * *Kernel*::**$SUCCESSIVE_DRIFTS_FATAL** = 200  
  * <a name="Ac_Async_Log"></a> *class* **Log** ( $stream = Ac\Async\STDOUT )  
    * *Methods*  
      * *Log*->**__construct**  ( $stream = Ac\Async\STDOUT )  
      * *Log*->**debug**  (  )  
      * *Log*->**log**  (  )  
      * *Log*->**info**  (  )  
      * *Log*->**warn**  (  )  
      * *Log*->**fatal**  (  )  
      * *Log*->**beep**  (  )  
  * <a name="Ac_Async_Process"></a> *class* **Process** ( $cmd )  
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
      * *Process*->**__construct**  ( $cmd )  
      * *Process*->**kill**  ( $signal = self::SIGNAL_SIGTERM )  
      * *Process*->**close**  (  )  
      * *Process*->**read**  ( *callable* $callback )  
      * *Process*->**readStdout**  ( *callable* $callback )  
      * *Process*->**readStderr**  ( *callable* $callback )  
      * *Process*->**write**  ( $data, *callable* $callback = null )  
    * *Static Methods*  
      * *Process*::**spawn**  ( $cmd )  
  * <a name="Ac_Async_Select"></a> *class* **Select** ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 )  
    * *Constants*  
      * *Select*::**CHUNK_SIZE** = 8192
      * *Select*::**READ_BUFFER_SIZE** = 0
      * *Select*::**WRITE_BUFFER_SIZE** = 0
      * *Select*::**IDLE** = 0
      * *Select*::**ACTIVE** = 1
      * *Select*::**DONE** = 3
    * *Methods*  
      * *Select*->**__construct**  ( $timeoutSeconds = 0, $timeoutMicroseconds = 200000 )  
      * *Select*->**select**  (  )  
      * *Select*->**addIdleCallback**  ( *callable* $callback )  
      * *Select*->**removeIdleCallback**  ( *callable* $callback )  
      * *Select*->**addActiveCallback**  ( *callable* $callback )  
      * *Select*->**removeActiveCallback**  ( *callable* $callback )  
      * *Select*->**addDoneCallback**  ( *callable* $callback )  
      * *Select*->**removeDoneCallback**  ( *callable* $callback )  
      * *Select*->**addCallbackReadable**  ( *callable* $callback, $stream = null )  
      * *Select*->**removeCallbackReadable**  ( *callable* $callback )  
      * *Select*->**addCallbackWritable**  ( *callable* $callback, $stream = null )  
      * *Select*->**removeCallbackWritable**  ( *callable* $callback )  
      * *Select*->**addCallbackExceptable**  ( *callable* $callback, $stream = null )  
      * *Select*->**removeCallbackExceptable**  ( *callable* $callback )  
      * *Select*->**addReadable**  ( $stream )  
      * *Select*->**removeReadable**  ( $stream )  
      * *Select*->**numReadables**  (  )  
      * *Select*->**addWritable**  ( $stream )  
      * *Select*->**removeWritable**  ( $stream )  
      * *int* *Select*->**numWritables**  (  )  
      * *bool* *Select*->**addExceptable**  ( $stream )  
      * *bool* *Select*->**removeExceptable**  ( $stream )  
      * *int* *Select*->**numExceptables**  (  )  
  * <a name="Ac_Async_Stream"></a> *abstract* *class* **Stream**  
    * *Static Methods*  
      * *Stream*::**openProcess**  ( $cmd )  
      * *Stream*::**openReadable**  ( $url = "php://temp" )  
      * *Stream*::**openWritable**  ( $url = "php://temp" )  
      * *Stream*::**openDuplex**  ( $url = "php://temp" )  
      * *Stream*::**spawnProcess**  ( $cmd )  
      * *Stream*::**read**  ( $stream, *callable* $callback )  
      * *Stream*::**readAndParse**  ( $stream, *[StringParser](#Ac_Async_StringParser)* $parser, *callable* $callback )  
      * *Stream*::**readLines**  ( $stream, *callable* $callback )  
      * *Stream*::**write**  ( $stream )  
  * <a name="Ac_Async_StringParser"></a> *class* **StringParser** ( $delim = "\n" )  
    * *Properties*  
      * *StringParser*->**delim**  
      * *StringParser*->**bytes**  
    * *Methods*  
      * *StringParser*->**__construct**  ( $delim = "\n" )  
      * *StringParser*->**write**  ( $str )  
      * *StringParser*->**end**  (  )  
      * *StringParser*->**bufferAppend**  ( $str )  
      * *StringParser*->**bufferClear**  (  )  
