<?php
namespace toolbox;
class overloaded_internal {

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

        utils::increaseStatCount('overload_counts', date('Y-m-d H:i').':00');

        $page = page::get();
        $page->clearViews()
            ->addView(function(){

                menu::get('top_nav')
                    ->setActive(false)
                    ->render();

            }, 'nav')
            ->setMainView('main/app.php')
            ->set('title', config::get()->getConfig('app_name').' is Currently Overloaded!')
            ->set('error', 'Please try loading the page again or come back at a later time.')
            ->setHttpResponseCode('503')
            ->addView('error-retry.php', 'content');

        $error = messages::readMessages();
        if(isset($error['error']) && isset($error['error'][0])){
             $page->set('error', $error['error'][0]);
        }

    }

}
