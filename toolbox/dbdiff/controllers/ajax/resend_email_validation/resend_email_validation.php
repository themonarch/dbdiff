<?php
namespace toolbox;
class resend_email_validation_controller {


    public function __construct() {


        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            return $this->invalid();
        }


        //if email already validated, reload the page
        if(user::getUserLoggedIn()->hasValidatedEmail()){
            messages::setSuccessMessage('Your email has already been verified.');
            utils::redirectTo('/account');
        }


        //send email if not recently sent
        try{//try to send
            user::getUserLoggedIn()->sendEmailVerification();
        }catch(userException $e){
            //user is doing that too much
            return $this->invalid($e->getMessage());
        }

        page::get()
            ->addView(function($tpl){ ?>
                <div class="messages messages-success">
                    An email has been sent to <b><?php
                        echo htmlspecialchars(user::getUserLoggedIn()->getEmail());
                    ?></b>. Please
                    click on the validation link in the email to verify your email address.
                </div>
            <?php }, 'content');

    }

    public function invalid($msg = null){
        if($msg !== null)
            messages::setErrorMessage($msg, 'resend_email');
        page::get()
            ->addView(function($tpl){
                messages::printMessages('resend_email');
            }, 'content');
    }


}
