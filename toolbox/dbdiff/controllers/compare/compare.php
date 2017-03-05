<?php
namespace toolbox;
class compare_controller {

    function __construct(){
		title::get()->setSubtitle('Database Diff Results');


		//some spacing
		page::get()->addView(function(){ ?>
			<div class="catchall spacer-2"></div>
		<?php }, 'content-narrow');

        //initializations
        $profile_id = router::get()->getParam('profile_id');
        $widget = widgetHelper::create();
		$sync = sync::get($profile_id);

try{//connect
	$source_conn = $sync->getSourceConnection();
	$source_conn->connect();
}catch(connectionException $e){
	messages::setErrorMessage($e->getMessage(), 'quick_connect-0');
	utils::redirectTo('/compare/'.$sync->getID().'/edit');
	//router::get()->to('/compare/'.$sync->getID().'/edit');
}

try{//connect
	$target_conn = $sync->getTargetConnection();
	$target_conn->connect();
}catch(connectionException $e){
	messages::setErrorMessage($e->getMessage(), 'quick_connect-1');
	utils::redirectTo('/compare/'.$sync->getID().'/edit');
	//router::get()->to('/compare/'.$sync->getID().'/edit');
}

$sync->updateLastViewed();
		$widget
            ->set('grid_classes', '')
			->set('profile_id', $profile_id)
			->set('title', $sync->getName())
			->addView(function(){ ?>
<div class="widget-header-controls left">
<a class="btn btn-small btn-silver" href="/"><i class="icon-angle-double-left"></i> Back</a>
</div>
			<?php }, 'header')
			->set('class', 'style4')
            ->set('widget_margin', false)
            ->add('dbdiff/sync_profile-compare.php', 'widget-reload.php');



		//some spacing
		page::get()->addView(function(){ ?>
			<div class="catchall spacer-2"></div>
			<div class="catchall spacer-5"></div>
<div class="header-line style2">
    <div class="inner">Tables You've Excpluded From This Comparison</div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-1"></div>
		<?php }, 'content-narrow');

		//add sql runner widget
		widgetHelper::create()
			->set('title', 'Hidden Tables')
			->set('class', 'style4')
			->set('profile_id', $profile_id)
			->set('only_excluded_table', true)
			->addView(function(){ ?>
<div class="widget-header-controls left">
<button class="btn btn-small btn-silver collapse" href="/"><i class="icon-down-open-big single"></i></button>
</div>
			<?php }, 'header')
			->set('style_widget_content', 'display: none;')
			->add('dbdiff/sync_profile-compare.php', 'widget-reload.php');






		//some spacing
		page::get()->addView(function(){ ?>
			<div class="catchall spacer-2"></div>
			<div class="catchall spacer-5"></div>
<div class="header-line style2">
    <div class="inner">SQL History for this Comparison</div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-1"></div>
		<?php }, 'content-narrow');

		//add sql runner widget
		widgetHelper::create()
			->set('title', 'SQL Execution History')
			->set('class', 'style4')
			->add('dbdiff/sql_history.php', 'widget-reload.php', 'sql_runner');

		//some spacing
		page::get()->addView(function(){ ?>
			<div class="catchall spacer-2"></div>
			<div class="catchall spacer-5"></div>
<div class="header-line style2">
    <div class="inner">Recent Diffs</div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-1"></div>
		<?php }, 'content-narrow');

		// recent comparisons widget
		widgetHelper::create()
            ->set('class', 'style4')
			->set('title', 'My Recent Comparisons')
			->add('dbdiff/recent_comparisons.php', 'widget-reload.php', 'recent_comparisons');

    }


    static function passThru(){
        title::get()
            ->addCrumb('Compare');

		router::get()->extractParam('profile_id');

		//need an active session beyond this point
		accessControl::get()
            ->removeRequired('member')//login not required for these pages
			->requires('session', function(){//but an active session is
			    messages::setErrorMessage('Your session has expired or cookies were deleted.');
			    utils::redirectTo('/login');
			});

		//active guest and members have a session
		if(user::isUserLoggedIn()){
			accessControl::get()->grant('session');

			try{//check if user has access to requested sync id
				user::getUserLoggedIn()->getSyncProfile(router::get()->getParam('profile_id'));
			}catch(userException $e){
				router::get()->toInternal('access_denied');
			}
		}

    }

}
