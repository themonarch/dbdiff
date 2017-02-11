<?php
namespace toolbox;
class change_password_controller {

    public function __construct() {

        $user = user::getUserLoggedIn();

        if($_SERVER['REQUEST_METHOD'] !== 'POST' ){
            return $this->invalid();
        }

        if(!isset($_POST['current_password']) || !user::checkPass($user->getEmail(), $_POST['current_password'])){
            messages::setWrong('Wrong password. Please enter your current '
                .config::get()->getConfig('app_name').' password', 'current_password');
            return $this->invalid();
        }

        validator::setForm($_POST);
        validator::validate('password');
        validator::validate('password2');
        if(!validator::isValid()){
            return $this->invalid();
        }

        db::query('update `users` set `password_hash` = '.db::quote(utils::hash($_POST['password'])).'
        where `user_id` = '.$user->getID());

        return $this->success();

    }

    function success(){
            messages::setSuccessMessage('Your password has now been changed.', 'change_password');
            $this->invalid();
    }

    public function invalid($msg = null){
        unset($_POST['current_password']);
        form::storeValues($_POST);
        if($msg !== null)
            messages::setErrorMessage($msg, 'change_password');
        page::get()
            ->addView('elements/change_password.php');
    }

    function showOriginalForm($msg = null){
        if($msg !== null)
            messages::setSuccessMessage($msg, 'change_email');
        page::get()
            ->addView('elements/change_email_form.php');
    }

}
