<?php
namespace toolbox;
class logout_controller {

    static function setup(){
    }

    function __construct(){
    	$r = '/';
		if(isset($_REQUEST['r'])){
			$r = $_REQUEST['r'];
		}
        $ch = new cookieHelper();
        $ch->destroyCookie('login');
        //messages::setSuccessMessage('Thanks for visiting. Bye!');
        utils::redirectTo($r);

    }

    static function passThru(){

    }

}
