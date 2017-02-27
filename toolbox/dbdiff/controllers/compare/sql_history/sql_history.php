<?php
namespace toolbox;
class sql_history_controller {

    function __construct(){

        //initializations
        title::get()->addCrumb('View SQL');

        widgetHelper::create()
            ->set('title', 'View SQL')
            ->set('class', 'style3')
            ->add(function($tpl){ ?>
            	<div class="form_panel style3" style="text-align: center;">
            		<div style="white-space: pre-wrap; display: inline-block; margin: 0px auto; text-align: left;"><?php
						$sql_row = db::query('select * from `sql_history`
							where `id` = '.db::quote(router::get()->getParam('sql_id')))->fetchRow();

            			echo utils::htmlEncode($sql_row->sql);
            		?></div>
            	</div>

            <?php }, 'minimal.php', utils::isAjax());

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
