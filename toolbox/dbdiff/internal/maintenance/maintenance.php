<?php
namespace toolbox;
class maintenance_internal {

    static function setup(){
        router::get()
            ->setMaxParams(false)
            ->setMinParams(false);
        accessControl::get()
            ->clearRequirements();
        title::get()
            ->addCrumb('503 - Temporary Maintenance');
    }

    function __construct(){




        $page = page::get();
        $page->clearViews()
            ->addView(function(){

                menu::get('top_nav')
                    ->setActive(false)
                    ->render();

            }, 'nav')
            ->setMainView('main/app.php')
            ->set('title', config::get()->getConfig('app_name').' is Undergoing Maintenance')
            ->set('error', 'We are currently doing some maintenance on the site. Please try loading the page again or come back in a few minutes.')
            ->setHttpResponseCode('503')
            ->addView('error-retry.php', 'content');

        $error = messages::readMessages();
        if(isset($error['error']) && isset($error['error'][0])){
             $page->set('error', $error['error'][0]);
        }

    }

}
