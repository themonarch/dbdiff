<?php
namespace toolbox;
class account_controller {

    static function setup(){

    }

    function __construct(){

        title::get()->setSubtitle('Account Settings');

        sidebar::get('account')
            ->setActive('Account Settings');

            page::get()
                ->set('subtitle', false)
                ->addView(function(){
                   sidebar::get('account')->render();
                }, 'pre-pre-content')
                ->addView(function(){

                }, 'content-narrow');


    }

    static function passThru(){
		title::get()
			->setSubtitleDisabled()
			->addCrumb('Manage Account');
		sidebar::get('account')
			->addLink('Account Settings', '', '/account',
			'default.php',
			array('content-left' => '<i class="icon-list-alt"></i>'));
    }

}
