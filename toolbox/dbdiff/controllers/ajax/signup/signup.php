<?php
namespace toolbox;
class signup_controller {


    public function __construct() {

        if($_SERVER['REQUEST_METHOD'] !== 'POST' ){
            return $this->invalid();
        }elseif(user::ipHasAccount() && !isset($_REQUEST['g-recaptcha-response'])){
            return $this->invalid();
        }elseif(!$this->validForm($_POST)){
            return $this->invalid();
        }elseif(user::ipHasAccount()
            && $this->validRecaptcha($_REQUEST['g-recaptcha-response']) === false){
            return $this->invalid('Please check the recaptcha validation and try again.');
        }else{

            try{
                user::create($_POST);
            }catch(userException $e){
                return $this->invalid($e->getMessage());
            }

            //log the user in now.
            user::login(user::$last_created_user_id);

            $user = new user(user::$last_created_user_id);
			$user->setGrant('member');
            $user->sendEmailVerification();
            appUtils::sendEmail(
                config::getSetting('error_log')->emails,
                'New Signup: '.$user->getEmail(),
                'New user has signed up to '.config::get()->getConfig('app_name').'.'
                .'<br>Email Address: '.$user->getEmail()
                .'<br>Name: '.$user->getName()
                .'<br><br>Manage User: '
                .utils::getHost().'/admin/manage_users/'.$user->getStringID()
            );
            $user->setCustomValue('first_name', $_POST['first_name']);
            $user->setCustomValue('last_name', $_POST['last_name']);

            //messages::setSuccessMessage('You are now logged in as <b>'.htmlspecialchars($_POST['email']).'</b>.');
            if(isset($_POST['website']) && $_POST['website'] !== ''){
                utils::redirectTo('/add?domain='.urlencode($_POST['website']));
            }else{
                utils::redirectTo('/');
            }

        }



    }

    function validRecaptcha($recap_code){
        $response = curlWrapper::create()->addPostFields(
            array(
                'secret' => config::getSetting('reCAPTCHA-secret'),
                'response' => $recap_code,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        )->execute('https://www.google.com/recaptcha/api/siteverify');

        $response = json_decode($response);
        return $response->success;
    }

    static function passThru(){
        accessControl::get()->requires('guest', function(){
            utils::redirectTo('/');
        });
        accessControl::get()->removeRequired('member');
    }


    public function validForm(&$form){
        utils::trimArray($form);

        validator::setForm($form);

        validator::validate('email');

        validator::validate('first_name', 'general');
        validator::validate('last_name', 'general');

        validator::validate('password');

        validator::validate('password2');

        //\validator::validate('beta_key', 'general');

        return validator::isValid();
    }

    public function invalid($msg = 'Error. Please review your submission and try again.'){

        messages::setErrorMessage($msg, 'signup');
        form::storeValues();

        page::get()
            ->addView('signup.php');
    }


}
