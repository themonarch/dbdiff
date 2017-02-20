<?php
namespace toolbox;
class ajax_controller {

    static function setup(){

    }


    static function passThru(){
        page::get()
            ->setMainView('main/ajax.php');

		if(!utils::isAjax()){
			utils::redirectTo();
		}
    }

}
