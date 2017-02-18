<?php
namespace toolbox;
class bench_controller {

    function __construct(){
        page::get()
            ->addView(
            tasks::create(__CLASS__)
                ->setCheckInTime(1)
                ->setMaxCheckInTime(5)
				->set('config', config::get())
                ->setMaxExecutionTime(10)
                ->setTaskCallback(function(tasks $task, $row){
    	file_get_contents('../toolbox/library/db.class.php');
                }), 'content-narrow');



    }

}
