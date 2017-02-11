<?php
namespace toolbox;

class tasks extends page {
    protected $task_name;
    function __construct($task_name){

        if(trim($task_name) === ''){
            throw new toolboxException("Task must have a unique name passed to constructor!", 1);
        }

        $this->task_name = $task_name;

        parent::__construct();
        $this->setMainView('elements/tasks.php')
                ->setBenchmarking(false)
                ->setDebugLogging(false)
                ->setMaxExecutionTime(false)
                ->setMaxCheckInTime(60)
                ->setCheckInTime(10);

    }

    function setBenchmarking($bool){
        if($bool){
            bench::resume();
        }else{
            bench::pause();
        }

        return $this;
        return new tasks();
    }

    private $debug_mode = false;
    function setDebugLogging($bool){
        $this->debug_mode = $bool;

        return $this;
        return new tasks();
    }

    protected $max_execution_time = false;
    /**
     * integer number of seconds, or false for unlimited
     */
    function setMaxExecutionTime($int){
        $this->max_execution_time = $int;

        return $this;
        return new tasks();
    }

    function getMaxExecutionTime(){
        return $this->max_execution_time;
    }

    protected $max_checkin_time = 60;
    /**
     * integer number of seconds, or false for unlimited
     */
    function setMaxCheckInTime($int){
        $this->max_checkin_time = $int;

        return $this;
        return new tasks();
    }

    protected $checkin_time = 10;
    /**
     * integer number of seconds, or false for unlimited
     */
    function setCheckInTime($int){
        $this->checkin_time = $int;

        return $this;
        return new tasks();
    }

    protected $sql;
    protected $run_sql_once = true;
    function setTaskQuery($sql_string, $run_once = true){
        $this->run_sql_once = $run_once;
        $this->sql = $sql_string;

        return $this;
        return new tasks();
    }

    function logDebug($line){

    }

    function setTaskCallback(\closure $callback){
        $this->setCallback('task_callback', $callback);

        return $this;
        return new tasks();
    }

    function setNoResultView($view = null){
        $this->clearViews('no-results');
        if($view === null){
            $view = page::create()->set('notice', 'No tasks to run!')->set('container_style', 'style-1')->addView('elements/notice.php');
        }
        $this->addView($view, 'no-results');
    }

    function getCallback($callback_name){

        $args = func_get_args();

        $args[0] = $this;

        if(isset($this->callbacks[$callback_name])){
            return call_user_func_array($this->callbacks[$callback_name], $args);
            //return $this->callbacks[$callback_name]($this);
        }

        return false;
    }

    function hasCallback($callback_name){
        if(isset($this->callbacks[$callback_name])){
            return true;
        }

        return false;
    }

    public $callbacks;
    function setCallback($callback_name, \closure $callback){
        $this->callbacks[$callback_name] = $callback;

        return $this;
        return new tasks();
    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = null){
        if($name === null){
            return new self($name);
        }

        self::$instances[$name] = new self($name);

        return self::$instances[$name];
        return new tasks();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    public static $instances = array();
    public static function get($name = 'singleton'){
        if(!isset(self::$instances[$name])){
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
        return new tasks();
    }

}
