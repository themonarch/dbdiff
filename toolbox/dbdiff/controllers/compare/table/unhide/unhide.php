<?php
namespace toolbox;
class unhide_controller {

    function __construct(){

        title::get()->addCrumb('Unhide Table');

        widgetHelper::create()
            ->add(function($tpl){



		        //initializations
		        $profile_id = router::get()->getParam('profile_id');
		        $table_name = router::get()->getParam('table_name');
		        $sync = sync::get($profile_id);//we already know it belongs to current user (see compare.php passthru)

				$sync->includeTable(router::get()->getParam('table_name'));

				messages::output('Table is unhidden.', 'success', 'style2');


			}, 'minimal.php', utils::isAjax());

    }

	static function passThru(){

	}

}
