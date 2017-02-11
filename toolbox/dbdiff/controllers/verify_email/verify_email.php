<?php
namespace toolbox;
class verify_email_controller {

    public function __construct() {

        page::get()
            ->set('title', 'Verify Email');


        $router = router::get();
        if($router->getParam('user_id_string') === false){
            return $this->invalid();
        }
        if($router->getParam('email_verification_token') === false){
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
                            and `token` = '.db::quote($router->getParam('email_verification_token')).'
                            and `token_type` = "email_verification"')->fetchRow();

        if($user->isEmailValidated()){
            return $this->success('This email was already previously validated.');
        }elseif($token_data === null){
            return $this->invalid();
        }elseif($token_data->used === '1'){
            return $this->invalid();
        }else{
            db::query('update `users` set `is_email_validated` = 1
                            where `user_id` = '.$user->getID());
            db::query('update `user_tokens` set `used` = 1
                            where `user_id` = '.$user->getID().'
                            and `token_type` = "email_verification"
                            and `token` = '.db::quote($token_data->token));
            return $this->success();
        }


    }

    function success($msg = 'This email address has been verified successfully.'){

        page::get()
            ->set('title', 'Email Verified')
            ->set('msg', $msg)
            ->addView(function($tpl){ ?>
<div class="section centered">
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2 style="text-align: center;">Email Verified</h2>
            </div>
            <div class="section-content">
                <div class="messages messages-success" style="display: block;">
                    <div class="message"><?php echo $tpl->msg; ?></div>
                </div>
                <div style="text-align: center;">
                    <a href="/account" class="btn btn-link">Back to Account</a>
                </div>
            </div>
        </div>
    </div>
</div>
            <?php }, 'content')
            ->renderViews();

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
        $router->extractParam('email_verification_token');

        accessControl::get()->removeRequired('member');

    }



}
