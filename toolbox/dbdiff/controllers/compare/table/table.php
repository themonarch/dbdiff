<?php
namespace toolbox;
class table_controller {

	static function setup(){
		router::get()
			->setMaxParams(1)
			->setMinParams(1);
	}

    function __construct(){


		router::get()->extractParam('table_name');

        //initializations
        $profile_id = router::get()->getParam('profile_id');
        $table_name = router::get()->getParam('table_name');
        $sync = sync::get($profile_id);

        $source_conn = $sync->getSourceConnection();
        $sync->connectSource();

        $target_conn = $sync->getTargetConnection();
        $sync->connectTarget();

        $source_create = $sync->getSourceCreate($table_name);
        $target_create = $sync->getTargetCreate($table_name);

        $diff = explode("\n", utils::htmlDiff($source_create, $target_create));
        foreach($diff as $key => &$value){
            $diff[$key] = (object)array('line' => $value);
        }


		//add sql runner widget
		widgetHelper::create()
			->setHook('sql_runner')
			->set('title', 'SQL History')
			->add(function(){
				echo 'sql runner';
			}, 'widget-reload.php', 'sql_runner');

		if(
			utils::isPost()
			&& isset($_POST['alter'])
		){

            $total_execution_limit = 300;//may set harsher limits in the future
            set_time_limit($total_execution_limit);

		    //get selected db
		    if($_POST['alter'] === 'prod'){
		        $db_id = $sync->getData('target_conn_id');
		    }elseif($_POST['alter'] === 'dev'){
                $db_id = $sync->getData('source_conn_id');
		    }else{
                throw new softPublicException('Invalid form submit, please try again.');
		    }

			//get connection id
			$connection_id = db::query('select connection_id() as `connection_id`', $db_id)->fetchRow()->connection_id;

			//loop each sql for selected db
			foreach($_POST['sqls'][$_POST['alter']] as $key => $sql){



                //add to sql history table with connection id
                db::query('INSERT INTO `sql_history` (
                        `sync_id`,
                        `direction`,
                        `server_session_id`,
                        `sql`
                    ) VALUES (
                        '.db::quote($sync->getID()).',
                        '.db::quote($_POST['alter']).',
                        '.db::quote($connection_id).',
                        '.db::quote($sql).'
                    )');

                //execute the sql asyncroniously?
                $async_query = db::asyncQuery($sql, $db_id);

                //check for error using mysqli error

                //update query history as running

                //while not reaped
                    //sleep for 3 seconds

                    //update last checked status?

                    //if over time limit for single sql
                    //(($total_execution_limit-10)/$number_of_sqls), set status to unknown
                    //and msg that it wasn't complete after X seconds

                //if reaped
                    //update to success

			}


		}else{//don't render the sql runner
			page::get()->clearViews('sql_runner');

		}


        $widget = widgetHelper::create()
			->set('source_name', $source_conn->getName())
			->set('source_db', $sync->getSourceDB())
			->set('target_name', $target_conn->getName())
			->set('target_db', $sync->getTargetDB())
			->set('source_create', $source_create)
			->set('target_create', $target_create)
			->set('diff', $diff)
			->set('sync_id', $sync->getID())
			->set('table_name', $table_name)
            ->add('/dbdiff/sync_profile-schema.php', 'minimal.php', utils::isAjax());

    }


    static function passThru(){
        title::get()
            ->addCrumb('Table');


    }

}
