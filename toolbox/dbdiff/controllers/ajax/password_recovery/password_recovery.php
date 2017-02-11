<?php
namespace toolbox;
class password_recovery_controller {

    static function setup(){
        accessControl::get()
            ->requires('guest')
            ->removeRequired('member');

    }

    public function __construct() {

        if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])){
            return $this->invalid();
        }
		$user = null;
        //validate email exists
        try{
            $user = user::getByEmail($_POST['email']);
        }catch(userNotFound $e){
            return $this->invalid('That account was not found.');
        }


        try{//send recovery email
            $user->sendPasswordResetEmail();
        }catch(userException $e){
            //can't send
            return $this->invalid($e->getMessage());
        }

        //success
        messages::setSuccessMessage('An email has been sent to <b>'.htmlspecialchars($_POST['email'], ENT_QUOTES).'</b> with instructions on how to reset the password.');
        return page::get()->addview(function(){ ?>
            <div class="section centered colored padded">
                <div class="contents">
                    <div class="contents-inner">
                        <div class="section-header">
                            <h2>Password Recovery</h2>
                        </div>
                        <div class="section-content">
                            <?php messages::printMessages(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-close-overlay="" class="close_overlay"></div>
            <?php
        });


    }

    public function invalid($msg = null){
        form::storeValues($_POST);
        if($msg !== null)
            messages::setErrorMessage($msg, 'forgot_password');
        page::get()
            ->addView('password_recovery_form.php');
    }



}
