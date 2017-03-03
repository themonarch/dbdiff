<?php
namespace toolbox;
class reset_controller {

    public function __construct() {

        page::get()
            ->set('title', 'Reset Password');

        $router = router::get();
        if($router->getParam('user_id_string') === false){
            return $this->invalid();
        }
        if($router->getParam('password_reset_token') === false){
            return $this->invalid();
        }

        try{//validate user
            $user = user::getByStringID($router->getParam('user_id_string'));
        }catch(userNotFound $e){
            return $this->invalid();
        }

        //validate token
        $token_data = db::query('select * from `user_tokens`
                            where `user_id` = '.$user->getID().'
                            and `token` = '.db::quote($router->getParam('password_reset_token')).'
                            and `token_type` = "password_reset"')->fetchRow();

        if($token_data === null){
            return $this->invalid();
        }elseif($token_data->used === '1'){
            return $this->invalid();
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST' ){
            //confirm new pass
            validator::setForm($_POST);
            validator::validate('password');
            validator::validate('password2');
            if(!validator::isValid()){
                return $this->showForm();
            }

            db::query('update `users` set `password_hash` = '.db::quote(utils::hash($_POST['password'])).',
            `is_email_validated` = 1
            where `user_id` = '.$user->getID());

            db::query('update `user_tokens` set `used` = 1
                            where `user_id` = '.$user->getID().'
                            and `token_type` = "password_reset"
                            and `token` = '.db::quote($router->getParam('password_reset_token')));

            //succes
            messages::setSuccessMessage('Your password has now been changed.', 'reset_password');
            return page::get()
                ->addView(function($tpl){ ?>
<div class="section centered">
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2 style="text-align: center;">Success!</h2>
            </div>
            <div class="section-content">
                <div class="form_panel">
                <?php messages::printMessages('reset_password', 'style5'); ?>
                <div class="catchall spacer-2"></div>
                <div style="text-align: center;">
                    <a class="btn btn-link btn-medium" href="/login">Continue to Login</a>
                </div>
                </div>

            </div>
        </div>
    </div>
</div>
                <?php }, 'content');
        }


        return $this->showForm();


    }




    function showForm(){

        page::get()
            ->addView('elements/password_reset_form.php', 'content-narrow');

    }

    function invalid($msg = 'Wrong or expired link.'){
        page::get()
            ->set('error', $msg)
            ->addView('error-generic.php', 'content')
            ->renderViews();
    }

    static function passThru(){

        $router = router::get();
        $router->extractParam('user_id_string');
        $router->extractParam('password_reset_token');

        router::get()->extractParam('password_reset_token');
        accessControl::get()->removeRequired('member');

    }



}
