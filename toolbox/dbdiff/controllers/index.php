<?php
namespace toolbox;
class index_controller {

    static function setup(){

		sidebarV2::get('top_nav')->setActive('Home');

        accessControl::get()//no login required for the homepage
            ->removeRequired('member');

    }

    function __construct(){

		//guests and members have a different homepage
		//so we internally re-route them to their respective homepage
        if(user::isMemberLoggedIn()){//visitor is a member

			//take them to the dashboard in toolbox/APP/internal/dashboard
            router::get()->toInternal('dashboard');

        }else{//visitor is a guest

        	//take them to the homepage in toolbox/APP/internal/homepage
            router::get()->toInternal('homepage');

        }
	}


	static function passThru(){

	}

}