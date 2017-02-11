<?php
namespace toolbox;
class _404_internal {

    static function setup(){
        router::get()
            ->setMaxParams(false)
            ->setMinParams(false);
        accessControl::get()
            ->clearRequirements();
        title::get()
            ->addCrumb('404 - Page Not Found');
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
            ->set('title', '404: Page Not Found!')
            ->set('error', 'The page you are looking for does not exist.')
            ->setHttpResponseCode('404')
            ->addView('error-generic.php', 'content');

        $error = messages::readMessages();
        if(isset($error['error']) && isset($error['error'][0])){
             $page->set('error', $error['error'][0]);
        }

    }

}
