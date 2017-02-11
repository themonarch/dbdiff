<?php
namespace toolbox;
class forgot_password_controller {

    static function setup(){
        accessControl::get()
            ->removeRequired('member')
            ->requires('guest');
    }


    function __construct(){

        page::get()
            ->addView(
            page::create()
                ->addView('elements/section-centered.php')
                ->addView(
            function(){ ?>
                <div style="position: relative; margin: 0 auto; text-align: center;" id="forgot_password">
                    <?php page::get()->render('password_recovery_form.php'); ?>
                </div>
            <?php }), 'content-narrow');


    }

}
