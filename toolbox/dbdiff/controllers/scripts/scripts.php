<?php
namespace toolbox;
class scripts_controller {

    static function passThru(){
        //dont have to be logged in
        accessControl::get()->removeRequired('member');

        //kick out non-webmasters
        accessControl::get()->requires('webmaster');

        bench::pause();//dont log queries

        //settings to make sure long running scripts don't time out
        ignore_user_abort(true);
        set_time_limit(0);
        ob_implicit_flush(true);

        db::query('SET SESSION wait_timeout = 3000');
        ini_set('memory_limit','300M');

        page::get()
            ->clearViews('post-body-header')
            ->clearViews('ads')
            ->clearViews('analytics_codes')
            ->clearViews('analytics_codes-header');


    }

}
