<?php
namespace toolbox;
class change_email_controller {


    public function __construct() {

        $user = user::getUserLoggedIn();
        if($_SERVER['REQUEST_METHOD'] !== 'POST' ){
            return $this->invalid();
        }


        if(isset($_POST['action']) && $_POST['action'] === 'cancel'){
            db::query('delete from `user_tokens` where `user_id` = '.$user->getID().'
                        and `token_type` = "email_change"');
            $user->deleteValue('email_change');
            return $this->showOriginalForm('Your email change request has been cancelled.');

        }

        $skip_passcheck = false;
        if(isset($_POST['action'])
            && $_POST['action'] === 'resend'
        ){
            if($email_change = $user->getCustomValue('email_change')){
                $_POST['email'] = $email_change;
                $skip_passcheck = true;
            }else{
                utils::redirectTo('/account');
            }
        }

        validator::setForm($_POST);
        $is_valid = true;
        //validate email formatting
        if(validator::validate('email')){


        }else{
            $is_valid = false;
        }

        //validate correct password
        if($skip_passcheck === false && user::checkPass(
            $user->getEmail(),
            isset($_POST['password']) ? $_POST['password']: null
        ) === false){
            messages::setWrong('Wrong password. Please enter your current '.config::get()->getConfig('app_name').' password', 'password');
            $is_valid = false;
        }


        if(!$is_valid){
            return $this->invalid();
        }



        try{//try to send
            $user->sendEmailChange(trim($_POST['email']));
        }catch(userException $e){
            //user is doing that too much
            return $this->invalid($e->getMessage());
        }

        return $this->success();

    }

    function success(){
        page::get()
            ->set('email_address', $_POST['email'])
            ->addView(function($tpl){ ?>
                <div class="messages messages-success">
                    An email has been sent to <b><?php echo htmlspecialchars($tpl->email_address); ?></b>. Please
                    click on the validation link in the email to verify your email address.
                </div>
            <?php }, 'content')
            ->set('pending_email', $_POST['email'])
            ->addView('elements/change_email_pending_form.php');
    }

    public function invalid($msg = null){
        form::storeValues($_POST);
        if($msg !== null)
            messages::setErrorMessage($msg, 'change_email');
        page::get()
            ->addView('elements/change_email_form.php');
    }

    function showOriginalForm($msg = null){
        if($msg !== null)
            messages::setSuccessMessage($msg, 'change_email');
        page::get()
            ->addView('elements/change_email_form.php');
    }

}
