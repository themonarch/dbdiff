<?php
namespace toolbox;
class compare_controller {

    function __construct(){

		page::get()->addView(function(){ ?>
			<a href="/" class=""><i class="icon-angle-double-left"></i> Back</a>
			<div class="catchall spacer-1"></div>
		<?php }, 'content-narrow');


        //initializations
        $profile_id = router::get()->getParam('profile_id');
        $widget = widgetHelper::create();
		$sync = sync::get($profile_id);
//connect
try{
	$source_conn = $sync->getSourceConnection();
	$source_conn->connect();
	$target_conn = $sync->getTargetConnection();
	$target_conn->connect();
}catch(connectionException $e){
	throw new softPublicException($e->getMessage());
}
$sync->updateLastViewed();
		$widget
            ->set('grid_classes', '')
			->set('profile_id', $profile_id)
			->set('title', $sync->getName())
			->set('class', 'style4')
            ->set('widget_margin', false)
            ->add('dbdiff/sync_profile-compare.php', 'widget-reload.php');


    }


    static function passThru(){
        title::get()
            ->addCrumb('Compare');

		router::get()->extractParam('profile_id');

		//need an active session beyond this point
		accessControl::get()
            ->removeRequired('member')//login not required for these pages
			->requires('session', function(){//but an active session is
			    messages::setErrorMessage('Session has expired or wasn\'t created.');
			    utils::redirectTo('/login');
			});

		//active guest and members have a session
		if(user::isGuestLoggedIn() || user::isUserLoggedIn()){
			accessControl::get()->grant('session');

			try{//check if user has access to requested sync id
				user::getUserLoggedIn()->getSyncProfile(router::get()->getParam('profile_id'));
			}catch(userException $e){
				router::get()->toInternal('access_denied');
			}
		}

    }

}
