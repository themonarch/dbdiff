<?php
namespace toolbox;
class bench_controller {

    function __construct(){

        page::get()
            ->addView(
            tasks::create(__CLASS__)
                ->setCheckInTime(10)
                ->setMaxCheckInTime(15)
				->set('config', config::get())
                ->setMaxExecutionTime(100)
                ->setTaskCallback(function(tasks $task, $row){

db::query('INSERT INTO `test` (`data`) VALUES ('.db::quote(utils::getRandomString(150)).')');

                }), 'content-narrow');



    }

}
