<?php
namespace toolbox;
class delete_controller {

    public function __construct(){
		$comparison_id = router::get()->getParam('profile_id');

        $sync = sync::get($comparison_id);

		//delete it
		db::query('delete from `db_sync_profiles` where `id` = '.db::quote($sync->getID()));

		//show success msg
		widgetHelper::create()
            ->add(function(){
                messages::output('Deleted', 'success', 'style3');
            }, 'blank.php', true);


    }

	static function passThru(){


	}

}
