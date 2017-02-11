<?php
namespace toolbox;
class benchmark {

    function __construct() {
        $this->start_time = isset($_SERVER["REQUEST_TIME_FLOAT"]) ? $_SERVER["REQUEST_TIME_FLOAT"] : microtime(true);
        if(function_exists('getrusage')){
            $dat = getrusage();
            $this->PHP_RUSAGE = $dat["ru_utime.tv_sec"]*1e6+$dat["ru_utime.tv_usec"];
        }

    }

    private $timers = array();
    /**
     * Start benchmark for named logic
     */
    function startBench($logic_name){
        //if logic is currently running throw error
        if(isset($this->timers[$logic_name])){
            throw new benchmarkException('Timer for this logic is already running: '.$logic_name);
        }

        //start a new timer for this logic
        $this->timers[$logic_name] = new stopWatch();
    }

    function finishBench($logic_name){
        //if logic not running, throw error
        if(!isset($this->timers[$logic_name])){
            throw new benchmarkException('Timer for this logic is NOT running: '.$logic_name);
        }

        //get elpased time
        $elapsed_time = $this->timers[$logic_name]->getElapsedTime();
        $this->timers[$logic_name] = null;//destroy stopWatch

        $this->callFinishCallback($logic_name, $elapsed_time);

        return $elapsed_time;
    }

    private $elapsed_timer = false;
    function getSessionTime(){
        if($this->elapsed_timer === false){
            $this->elapsed_timer = stopWatch::create();
        }

        return $this->elapsed_timer->getSessionTime();
    }

    private $callback_function;
    function setfinishCallback($function){
        //if not a valid callback, throw error
        if(!is_callable($function)){
            throw new benchmarkException('Callback function muist be a callable function!');
        }

        $this->callback_function = $function;


        return $this;
        return new self();
    }

    function callFinishCallback($logic_name, $elapsed_time){
        //if not a valid callback, throw error
        if($this->callback_function == null){
            return false;
        }

        call_user_func($this->callback_function, $logic_name, $elapsed_time);
    }



    public function getRamUsage() {
        return round(memory_get_peak_usage()/1024/1024, 4);
    }

    public function getCpuLoad() {
        if(!function_exists('sys_getloadavg')){
            return 'N/A';
        }
        $usage = sys_getloadavg();

        return $usage[0];
    }


    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new self();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instance;
    public static function get(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
        return new self();
    }

}
class benchmarkException extends toolboxException {}
