<?php
namespace toolbox;
class table_controller {

	static function setup(){
		router::get()//require a table name param
			->setMaxParams(1)
			->setMinParams(1);
	}

    function __construct(){

		router::get()->extractParam('table_name');

        //initializations
        $profile_id = router::get()->getParam('profile_id');
        $table_name = router::get()->getParam('table_name');
        $sync = sync::get($profile_id);//we already know it belongs to current user (see compare.php passthru)

        $source_conn = $sync->getSourceConnection();
        $sync->connectSource();

        $target_conn = $sync->getTargetConnection();
        $sync->connectTarget();

		//add sql runner widget
		widgetHelper::create()
			->setHook('sql_runner-inner')
			->set('title', 'SQL History for `'.utils::htmlEncode($table_name).'`')
			->set('class', 'style4')
			->set('table', $table_name)
			->add('dbdiff/sql_history.php', 'widget-reload.php', 'sql_runner');

		page::get()->addView(function(){ ?>
			<div class="header-line style2">
			    <div class="inner">Alter History</div>
			    <div class="gradient-line"></div>
			</div>
			<div class="catchall spacer"></div>
			<?php page::get()->renderViews('sql_runner-inner'); ?>
		<?php }, 'sql_runner');

		if(
			utils::isPost()
			&& isset($_POST['alter'])
		){
			if(!isset($_POST['sqls']) || empty($_POST['sqls'][$_POST['alter']])){
                throw new softPublicException('Click the checkbox next to each SQL query you want to run.');
			}

            $total_execution_limit = 3000;//may set harsher limits in the future
            set_time_limit($total_execution_limit+10);

		    //get selected db
		    if($_POST['alter'] === 'prod'){
		        $db_id = $sync->getTargetConnection()->getDBID();
		    }elseif($_POST['alter'] === 'dev'){
                $db_id = $sync->getSourceConnection()->getDBID();
		    }else{
                throw new softPublicException('Invalid form submit, please try again.');
		    }

			//get connection id
			$connection_id = db::query('select connection_id() as `connection_id`', $db_id)->fetchRow()->connection_id;
			$individual_sql_limit = ($total_execution_limit)/count($_POST['sqls'][$_POST['alter']]);

			//update stuck queries
			$stuck_count = db::query('update `sql_history`
			set `status` = "unknown"
			where `sync_id` = '.db::quote($sync->getID()).'
			and `status` = "running"
			and `table` = '.db::quote($table_name).'
			and `date_updated` < date_sub(now(), interval 1 minute)')->rowCount();

			if($stuck_count > 0){//cancel pending queries if one got stuck
				db::query('update `sql_history`
					set `status` = "cancelled",
					`status_msg` = "Cancelled because one of the queries in the batch had an unknown status."
					where `sync_id` = '.db::quote($sync->getID()).'
					and `table` = '.db::quote($table_name).'
					and `status` = "pending"')->rowCount();

			}

			//if any running queries for this table, don't let user run more
			if(db::query('select * from `sql_history`
			where `sync_id` = '.db::quote($sync->getID()).'
			and `status` = "running"
			and `table` = '.db::quote($table_name))->rowCount() > 0){
				throw new softPublicException('There is a running query for this table, please wait for it to finish.');
			}


			$sql_ids = [];
			//loop each sql for selected db
			foreach($_POST['sqls'][$_POST['alter']] as $key => $sql){

                //add to sql history table with connection id
                $sql_id = db::query('INSERT INTO `sql_history` (
                        `sync_id`,
                        `direction`,
                        `table`,
                        `server_session_id`,
                        `sql`,
                        `date_updated`
                    ) VALUES (
                        '.db::quote($sync->getID()).',
                        '.db::quote($_POST['alter']).',
                        '.db::quote($table_name).',
                        '.db::quote($connection_id).',
                        '.db::quote($sql).',
                        now()
                    )')->last_insert_id();
					$sql_ids[$key] = $sql_id;

			}

			foreach ($sql_ids as $key => $sql_id) {
				$sql = $_POST['sqls'][$_POST['alter']][$key];

				//check query hasn't been cancelled
				$sql_row = db::query('select * from `sql_history` where `id` = '.$sql_id)->fetchRow();
				if($sql_row->status !== 'pending'){
					continue;
				}


                //execute the sql asyncroniously so we don't hang on long queries
                $async_query = db::asyncQuery($sql, $db_id);

                //start timer for this query
				$timer = stopWatch::create();

                //while query not finished
                while(!$async_query->isReady()){
                	if($timer->getElapsedTime() > $individual_sql_limit){//time limit hit
                		//update status to unknown
                		db::query('update `sql_history` set `status` = "unknown",
                		`status_msg` = '.db::quote('Waited '.$timer->getElapsedTime().' seconds with no response.')
                		.', `date_updated` = now()
                		where `id` = '.db::quote($sql_id));

                		break;//stop waiting on this query
                	}

            		//update status progress
            		db::query('update `sql_history` set `status` = "running",
            		`status_msg` = '.db::quote('Waiting  '.$timer->getElapsedTime().' seconds with no response.')
            		.', `date_updated` = now() where `id` = '.db::quote($sql_id));

                    //sleep for 3 seconds
                    sleep(3);

                }

                //if query completed
                if($async_query->isReady()){
                	try{
                		$sql_time = $timer->getElapsedTime();
	                	$result = $async_query->freeUpQuery();//free up instead of fetch b/c nothing to fetch!

	                    //update to success
	            		db::query('update `sql_history` set `status` = "completed",
	            		`status_msg` = '.db::quote('Completed successfully in  '.$timer->getElapsedTime().' seconds.')
	            		.', `date_updated` = now() where `id` = '.db::quote($sql_id));

					}catch(dbException $e){//if the db responded with an error
						//update sql history with error msg & failed status
	            		db::query('update `sql_history` set `status` = "failed",
	            		`status_msg` = '.db::quote($e->getMessage())
	            		.', `date_updated` = now() where `id` = '.db::quote($sql_id));

						//set pending queries to cancelled
	            		db::query('update `sql_history` set `status` = "cancelled",
	            		`status_msg` = "Cancelled because a previous query failed.",
	            		`date_updated` = now()
	            		where `sync_id` = '.db::quote($sql_id).'
	            		and `table` = '.db::quote($table_name).'
	            		and `status` = "pending"');

						//throw it to the browser
						throw new softPublicException($e->getMessage());
					}
				}

			}


		}else{//don't render the sql runner
			//page::get()->clearViews('sql_runner');

		}

		try{
	        $source_create = $sync->getSourceCreate($table_name);
	        $target_create = $sync->getTargetCreate($table_name);
		}catch(dbException $e){
			throw new softPublicException($e->getMessage());
		}

        $diff = explode("\n", utils::htmlDiff($source_create, $target_create));
        foreach($diff as $key => &$value){
            $diff[$key] = (object)array('line' => $value);
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
