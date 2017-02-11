<?php
namespace toolbox;
class bench_controller {

    function __construct(){
        $_SERVER['REQUEST_URI'] = '/test';

        page::get()
            ->addView(
            tasks::create(__CLASS__)
                ->setCheckInTime(1)
                ->setMaxCheckInTime(5)
				->set('config', config::get())
                ->setMaxExecutionTime(10)
                ->setTaskCallback(function(tasks $task, $row){
$id = utils::getRandomString(6);
db::connect(//connect to database
	    $task->config->getConfig('db', 'host'),
	    $task->config->getConfig('db', 'user'),
	    $task->config->getConfig('db', 'pass'),
	    $task->config->getConfig('db', 'name'),
	    $id
);
db::setDB();
db::disconnect($id);

                }), 'content-narrow');



    }

}
