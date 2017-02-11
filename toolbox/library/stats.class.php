<?php
namespace toolbox;

class stats {

    private $task_count = 0;//count for time segment (reset every invterval)
    private $total_task_count = false;//estimated amount of tasks to run
    private $total_task_progress = 0;//total tasks ran
    private $benchmark;
    private $store;
    private $timer;
    private $run_timer;
    private $config;
    private $pid;
    private $process_name = 'stats-';
    function __construct($name){
        //start timer
        $this->timer = stopWatch::create();
        $this->run_timer = stopWatch::create();
        $this->benchmark = benchmark::create();
        $this->store = store::get('stats', 'db');
        $this->config = config::get();
        $this->process_name .= $name;

        //delete stat if last update more than X minutes ago
        db::query('delete from `store` where `name` = '.db::quote($this->process_name).'
        AND `date_updated` <= date_sub(now(), interval 15 minute)');

        if($this->store->getValue($this->process_name) !== null){
            throw new toolboxException("Process already running for ".$this->process_name, 1);
        }else{
            $this->store->setValue($this->process_name, 1);
        }
        $this->pid = getmypid();
    }

    function printStat($interval = 10){
        $elapsed_time = $this->timer->getElapsedTime();
        if($interval > $elapsed_time){
            return false;
        }

        $tasks_per_sec = $this->getStat();

        $percentage = formatter::number_commas($this->total_task_progress);
        $percent = 'N/A';
        if($this->total_task_count !== false && $this->total_task_count !== 0){
            $percent = round(100*($this->total_task_progress/$this->total_task_count), 2);
            $percentage .= ' ('.$percent.'%)';
        }
        $peak_ram = $this->benchmark->getRamUsage();
        ?>
        <tr>
            <td><?php echo $this->timer->getSessionTime(); ?></td>
            <td><?php echo formatter::number_commas($tasks_per_sec); ?></td>
            <td><?php echo $percentage; ?></td>
            <td><?php echo $peak_ram; ?></td>
        </tr>
        <?php
        if($this->isRunning(0)){
            $this->store->setValue($this->process_name, json_encode(array(
                'Tasks Per Second' => $tasks_per_sec,
                'Tasks Completed' => $this->total_task_progress,
                'Progress' => $percent,
                'Peak RAM Usage' => $peak_ram,
                'PID' => $this->pid,
            )));
        }

        return true;
    }
    private $running_average = 0;
    private $running_average_count = 0;
    function getStat(){
        $elapsed_time = $this->timer->getElapsedTime();

        $tasks_per_sec = 0;
        if($elapsed_time != 0){
            $tasks_per_sec = round($this->task_count/$elapsed_time, 4);
        }

        //reset
        $this->total_task_progress += $this->task_count;
        $this->task_count = 0;
        $this->timer->startTimer();
        if($tasks_per_sec !== (float)0){
            $this->running_average = ($this->running_average * $this->running_average_count
                                            + $tasks_per_sec) / (++$this->running_average_count);
        }

        return $tasks_per_sec;
    }

    function getRunningAverage(){
        return round($this->running_average, 4);
    }

    private $running = true;
    private $limit = null;
    function isRunning($interval = 10, $limit = false){
        if($this->limit === null){
            $this->limit = $limit;
        }

        //if proc already stopped
        if($this->running === false){
            return false;
        }

        //if checkup interval not reached, assume still running
        if($interval > $this->run_timer->getElapsedTime()){
            return true;
        }

        //if execution time limit reached, stop
        if($this->limit !== false && $this->limit < $this->run_timer->getSessionTime()){
            $this->running = false;
            return false;
        }

        //checkup interval reached, reset timer for next checkup
        $this->run_timer->startTimer();


        //check if running flag still exists
        $current_proc_value = $this->store->getValue($this->process_name);
        if($current_proc_value === null){
            //flag as been removed, stop the proc.
            $this->running = false;
            return false;
        }

        //check if pid is still same incase two procs started running same time.
        if($current_proc_value !== '1'){
            $json = json_decode($current_proc_value, true);
            if(isset($json['PID']) && $json['PID'] !== $this->pid){
                $this->running = false;
                utils::vd($json['PID'] .' !== '. $this->pid);
                return false;
            }
        }

        return true;

    }

    function increaseTaskCount($count = 1){
        $this->task_count += $count;
    }

    function setTaskTotal($total_task_count){
        $this->total_task_count = (int)$total_task_count;
    }

    function printHeader(){
        if($this->total_task_count !== false){
            echo '<br>Starting '.formatter::number_commas($this->total_task_count).' tasks...<br>';
        }
        ?>
        <div style="max-width: 800px;">
            <table class="table style1">
            <thead><tr>
                <th>Elapsed Time
                    <br>(seconds)</th>
                <th>Tasks Per Second</th>
                <th>Total Progress</th>
                <th>Peak RAM</th>
            </tr></thead>
            <tbody>
        <?php
    }

    function printFooter(){
        $this->printStat(0);
        ?>
                </tbody>
            </table>
            <?php echo 'total tasks = '.formatter::number_commas($this->total_task_progress); ?>
            <?php echo ' | average tasks per second = '.formatter::number_commas($this->getRunningAverage()); ?>
        </div>
        <?php
        $this->stop();
    }

    function getTotalTasksCompleted(){
        return $this->total_task_progress;
    }

    function getTaskCount(){
        return $this->total_task_progress+$this->task_count;
    }

    function stop(){
        $this->store->deleteValue($this->process_name);
        $this->running = false;
    }

    function __destruct(){
        $this->stop();
    }

    static function deleteNoCheckin($proc, $seconds){
        $row = db::query('select * from `store` where `name` = '.db::quote('stats-'.$proc))->fetchRow();

        //if not running
        if($row === null){
            return false;
        }

        //if recently checked in
        if(strtotime($row->date_updated) >= strtotime('-'.$seconds.' seconds')){
            return false;
        }


        //delete the record
        db::query('delete from `store` where `id` = '.$row->id);

        $row->data = json_decode($row->value);

        return $row;

    }

    static function getStats($name){
        $json = '{"Tasks Per Second":0,"Tasks Completed":0,"Progress":0,"Peak RAM Usage":0,"PID":0}';
        $query = db::query('select * from `store` where `name` = '.db::quote($name));
        if($query->rowCount() !== 0){
            $row = $query->fetchRow()->value;
            if($row !== '' && $row !== '1'){
                $json = $row;
            }
        }

        return json_decode($json, true);

    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = 'singleton'){
        self::$instances[$name] = new self($name);
        return self::$instances[$name];
        return new stats();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($name = 'singleton'){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
        return new self();
        return new stats();
    }
}
