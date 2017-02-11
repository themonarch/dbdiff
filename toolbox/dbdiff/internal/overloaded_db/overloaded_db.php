<?php
namespace toolbox;
class overloaded_db_internal {

    static function setup(){
        router::get()
            ->setMaxParams(false)
            ->setMinParams(false);
        accessControl::get()
            ->clearRequirements();
        title::get()
            ->addCrumb('503 - Service Temporarily Unavailable');
    }

    function __construct(){


        utils::increaseStatCount('overload_db_counts', date('Y-m-d H:i').':00');


        $page = page::get();
        $page->clearViews()
            ->addView(function(){

                menu::get('top_nav')
                    ->setActive(false)
                    ->render();

            }, 'nav')
            ->setMainView('main/app.php')
            ->set('title', 'Could Not Connect to Database in Time!')
            ->set('error', 'Please try loading the page again or come back in a few minutes when there is less load on the server.')
            ->setHttpResponseCode('503')
            ->addView('error-retry.php', 'content');

        $error = messages::readMessages();
        if(isset($error['error']) && isset($error['error'][0])){
             $page->set('error', $error['error'][0]);
        }

    }

}
