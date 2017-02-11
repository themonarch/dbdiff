<?php
namespace toolbox;
class access_denied_internal {

    static function setup(){
        router::get()
            ->setMaxParams(false)
            ->setMinParams(false);
        accessControl::get()
            ->clearRequirements();
        title::get()
            ->addCrumb('403 Forbidden');
    }

    function __construct(){

		$page = page::get();

        $error = messages::readMessages();
        if(isset($error['error']) && isset($error['error'][0])){
             $page->set('error', $error['error'][0]);
        }else{
        	$page->set('error', 'You are not allowed to view this page.');
        }

		$page
            ->set('header', 'Access Denied!')
            ->setMainView('main/app.php')
            ->setHttpResponseCode('403')
            ->addView('error-generic.php', 'content')
            ->renderViews();
    }

	static function passThru(){
    	page::get()->clearViews();
	}
}
