<?php
namespace toolbox;
class kill_query_controller {

    function __construct(){

        title::get()->addCrumb('Kill Query');

        widgetHelper::create()
            ->add(function($tpl){
				$sql_row = db::query('select * from `sql_history`
					where `id` = '.db::quote(router::get()->getParam('sql_id')))->fetchRow();

				//if status not pending or running
				if(!in_array($sql_row->status, array('pending', 'running'))){
					messages::output('Cannot kill query with a status of '.$sql_row->status.'.', 'success', 'style2');
					return;
				}

				$cancelled = 0;
				if($sql_row->status == 'pending'){
					$cancelled = db::query('update `sql_history`
					set `status` = "cancelled", `status_msg` = "Query cancelled by user."
					where `id` = '.$sql_row->id.' and `status` = "pending"')->rowCount();
				}

				if($cancelled == 0){
					try{
						db::query('KILL '.$sql_row->server_session_id);
					}catch(dbException $e){
						throw new softPublicException($e->getMessage());
					}
				}


				messages::output('Query is kill.', 'success', 'style2');


			}, 'minimal.php', utils::isAjax());

    }

	static function passThru(){
		//extract sql_id
		if(!router::get()->extractParam('sql_id')){
			router::get()->toInternal('access_denied');
		}

		//validate it belongs to user
		if(db::query('select * from `sql_history`
			where `sync_id` = '.db::quote(router::get()->getParam('profile_id')).'
			and `id` = '.db::quote(router::get()->getParam('sql_id')))->rowCount() == 0
		){
			router::get()->toInternal('access_denied');
		}


	}

}
