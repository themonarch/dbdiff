<?php
namespace toolbox;
class index_controller {

    static function setup(){

		sidebarV2::get('top_nav')->setActive('Home');

        accessControl::get()//no login required for the homepage
            ->removeRequired('member');

    }

    function __construct(){

		//if a visitor is a member
        if(user::isMemberLoggedIn()){
			//take them to the dashboard
            router::get()->toInternal('dashboard');
        }else{
        	//take them to the homepage
            router::get()->toInternal('homepage');
        }
	}

	static function passThru(){
		page::get()->addView(function(){ ?>
	        <link rel="stylesheet" type="text/css" href="/assets/app/css/db.css">
			<script src="/assets/app/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
		<?php }, 'end_of_head_tag');
	}

}