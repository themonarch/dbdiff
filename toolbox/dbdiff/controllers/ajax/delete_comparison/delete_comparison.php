<?php
namespace toolbox;
class delete_comparison_controller {

    public function __construct(){
		$comparison_id = router::get()->getParam('comparison_id');

		//validate it exists and belongs to current user
		try{
			$sync = user::getUserLoggedIn()->getSyncProfile($comparison_id);
		}catch(userException $e){
			throw new softPublicException('Access denied!');
		}

		//delete it
		db::query('delete from `db_sync_profiles` where `id` = '.db::quote($sync->getID()));

		//show success msg
		page::get()->addView(function(){
            messages::output('Deleted', 'success', 'style3');
		});


    }

	static function passThru(){
		//extract the comparison id
		router::get()->extractParam('comparison_id');

		accessControl::get()->removeRequired('member');
	}

}
