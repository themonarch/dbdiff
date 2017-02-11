<?php namespace toolbox; ?>
<div class="header-line">
    <div class="inner">Running Task: <?php echo $task_name; ?></div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-2"></div>
<div style="max-width: 600px; margin: 0 auto;">
<?php
    try{
        @ob_end_flush();
    }catch(toolboxException $e){}

    $this->logDebug('Starting cron @ '.date('M d, Y g:i:s A T'));
    $this->logDebug('');

    //if previous cron still running
    if(($statsV2 = statsV2::deleteNoCheckin($task_name, $max_checkin_time)) !== false){
        //throw error
        throw new toolboxException('CRON: previous cron (PID: '.$statsV2->data->PID
            .') did not check in within '.$max_checkin_time.' seconds. Last checkin was '.$statsV2->date_inserted
            .'. Assuming it\'s dead/non-responsive. This cron will be
            set to stop and new one will be started.', 1);
    }

    $this->logDebug('Preparing setup...');
    $statsV2 = null;
    try{
        $statsV2 = statsV2::get($task_name);
    }catch(toolboxException $e){
        //previous cron still running fine..
        $this->logDebug('Previous cron still runnning, exiting...');
        utils::vd('Previous cron still runnning, exiting...');
        return;
    }

    if($run_sql_once && isset($sql)){
        $query = db::query($sql);
        $statsV2->setTaskTotal($query->rowCount());
    }
    ?>
    <table class="table style1">
        <thead><tr>
            <th>Elapsed Time
                <br>(Limit: <?php var_export($this->getMaxExecutionTime()); ?>)</th>
            <th>Tasks Per Second</th>
            <th>Total Progress
                <br>(Out of <?php echo formatter::number_commas($statsV2->getTaskTotal()); ?>)</th>
            <th>Peak RAM
                <br>(Limit: <?php echo ini_get('memory_limit'); ?>)</th>
        </tr></thead>
        <tbody>
    <?php
    //start the cron
    while($statsV2->isRunning($checkin_time, $max_execution_time)){
		if(isset($query)){
	        $row = $query->fetchRow();
	        if($row === null){
	            $statsV2->stop();
	        }else{
	            if($this->getCallback('task_callback', $row, $statsV2) !== false){
	            	$statsV2->increaseTaskCount();
				}
	        }
		}else{
			if($this->getCallback('task_callback', null, $statsV2) !== false){
            	$statsV2->increaseTaskCount();
			}
		}
        if($statsV2->hasPassed($checkin_time)){ ?>
        <tr>
            <td><?php echo $statsV2->getSessionTime(); ?></td>
            <td><?php echo formatter::number_commas($statsV2->getTasksPerSecond()); ?></td>
            <td><?php echo formatter::number_commas($statsV2->getTaskCount());
                    ?> (<?php echo $statsV2->getPercentage(); ?>%)</td>
            <td><?php echo $statsV2->getPeakRam(); ?></td>
        </tr>
        <?php
        }

    } ?>
    <tr>
        <td>Total Time: <b><?php echo $statsV2->getSessionTime(); ?></b></td>
        <td colspan="2">Tasks Completed: <b><?php echo formatter::number_commas($statsV2->getTotalTasksCompleted());
            ?>/<?php echo formatter::number_commas($statsV2->getTaskCount()); ?></b></td>
        <td>Peak RAM: <b><?php echo $statsV2->getPeakRam(); ?></b></td>
    </tr>
    <?php


    $statsV2->stop();

?>
    </tbody>
    </table>
</div>