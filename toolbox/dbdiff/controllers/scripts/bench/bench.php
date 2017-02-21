<?php
namespace toolbox;
class bench_controller {

    function __construct(){



		$query = db::asyncQuery('select sleeep(5)');
		sleep(1);
		utils::vdd($query->fetchRow());
		$timer = stopWatch::create();
		while(!$query->isReady()){
			sleep(1);
			utils::vd($timer->getElapsedTime());
		}

		if($query->isReady()){
			$result = $query->fetchRow();
		}

		utils::vdd($result);



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
