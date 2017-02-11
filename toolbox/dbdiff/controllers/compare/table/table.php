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
		$source_conn->connect();

		$target_conn = $sync->getTargetConnection();
		$target_conn->connect();

		$source_create = $sync->getSourceCreate($table_name);
		$target_create = $sync->getTargetCreate($table_name);

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
            ->add('/dbdiff/sync_profile-schema.php', 'minimal.php', utils::isAjax());

    }


    static function passThru(){
        title::get()
            ->addCrumb('Table');


    }

}
