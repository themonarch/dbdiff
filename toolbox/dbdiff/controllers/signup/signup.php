<?php
namespace toolbox;
class signup_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member')
            ->requires('guest', function(){
            	utils::redirectTo();
            });

        sidebarV2::get('top_nav')->setActive('Sign Up');

		title::get()
		    ->addCrumb('Sign Up');
    }

    function isValidRecaptcha(){

        if(!isset($_REQUEST['g-recaptcha-response']) || trim($_REQUEST['g-recaptcha-response']) == ''){
            return 'Recaptcha checkbox was not checked!';
        }



        $response = curlWrapper::create()->addPostFields(
            array(
                'secret' => config::getSetting('reCAPTCHA-secret'),
                'response' => $_REQUEST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        )->execute('https://www.google.com/recaptcha/api/siteverify');

        $json = json_decode($response);
        if(!isset($json->success)){
            throw new toolboxException('Unexpected response from google recaptcha api: '.$response);
        }
        if($json->success === true){
            return true;
        }

        return 'Recaptcha was not validated. Please try again.';

    }

	function isValid(){


        validator::validate('email');

        validator::validate('password');

        validator::validate('password2');


		if(!validator::isValid()){
            messages::setErrorMessage('There was an error with your submission.');
			return false;
		}

        if(($msg = $this->isValidRecaptcha()) !== true){
            messages::setErrorMessage($msg);
			return false;
		}

		return true;

	}

	function process(){
        try{
			//if guest account exists
			if(user::isGuestLoggedIn()){
				$guest = user::getUserLoggedIn();
				if(db::query('select * from `users` where `email_address` = '.db::quote($_POST['email']))->rowCount() > 0){
            		throw new userException('An account with this email already exists!');
				}

				//convert to member account
				db::query('update `users`
				set `email_address` = '.db::quote($_POST['email']).',
				`password_hash` = '.db::quote(password_hash($_POST['password'], PASSWORD_DEFAULT)).'
				where `user_id` = '.db::quote($guest->getID()));

				//get current encrpytion key
				$old_key = utils::getEncryptionKey();

				//generate new encryption key
				utils::generateEncryptionCookie($_POST['password']);

				//get all user's connections
				$connections = db::query('select * from db_connections
					where user_id = '.db::quote($guest->getID()));

				//re-encrypt all old passwords with new encryption key
				while($connection = $connections->fetchRow()){
					$old_pass = utils::decrypt($connection->password, $old_key);
					$new_pass = $guest->encrypt($old_pass);
					db::query('update db_connections
						set `password` = '.db::quote($new_pass).'
						where `connection_id` = '.db::quote($connection->connection_id));
				}
				$user = $guest;


			}else{
            	user::create(
            		$_POST, //create the user
            		true, //log them in
					true //generate an excryption cookie
				);
        		$user = new user(user::$last_created_user_id);
			}


        }catch(userException $e){
        	messages::setErrorMessage($e->getMessage());
            return false;
        }


		$user->setGrant('member');
        //$user->sendEmailVerification();

		return true;

	}

    function __construct(){




		if(utils::isPost()){
			if($this->isValid() && $this->process()){


			messages::setSuccessMessage('You now have your very own account.
			You\'re logged in and ready to go!');

			page::get()
				->clearViews('print_messages');
			widgetHelper::create()
				->add(function($tpl){ ?>
        	<div class="form_panel style2">
                <h2 style="text-align: center;">You're Awesome!</h2>
                <div class="catchall spacer-1"></div>
				<?php messages::printMessages('messages', 'style5'); ?>
                <div style="text-align: center;">
                    <a class="btn btn-link" href="/">Continue to Homepage</a>
                </div>
           	</div>
			<?php }, 'minimal.php', true);
			return;


			}else{
				formV2::storeValues($_POST);
			}
		}


        widgetHelper::create()
            ->setHook('inner')
			->set('title', 'Sign Up')
			->set('style', 'max-width: 420px; margin: 0 auto;')
			->set('class', 'style3')
            ->add(function($tpl){ ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
				<form class="form padding" method="post" action="/signup"
					data-ajax_form="#<?php echo $tpl->widget_id; ?>">
                        <?php
						messages::printMessages('messages', 'style5');
                        form::textField()
                                ->setTypeText()
                                ->setName('email')
                                ->setPlaceholder('')
								->setNote('We will send a verification link to your email.')
                                ->render(); ?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypePassword()
                                ->setName('password')
                                ->setPlaceholder('')
								->setNote('<b>IMPORTANT:</b> Your password will be your account\'s encryption key.
								We don\'t store passwords, so if you forget it, some account data will <b>not be recoverable!</b>')
                                ->render();?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypePassword()
                                ->setName('password2')
                                ->setLabel('Retype Password')
                                ->setPlaceholder('')
                                ->render();?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypeHidden()
                                ->setName('website')
                                ->render(); ?>
                            <div class="catchall spacer"></div>
<div class="form-element">
<div class="input-wrapper">
    <label>Check the Box to Prove You're Not a Robot: </label>
        <div style="margin: 0px auto; text-align: center; display: table;">
            <div class="g-recaptcha" data-sitekey="<?php echo config::getSetting('reCAPTCHA-site'); ?>"></div>
        </div>
   </div>
</div>

                        <div style="text-align: right; margin: 15px 0px 0;">
                            <div class="grid-6">
                            <div class="" style="text-align: center;">Already have an account?
                                <a data-overlay-id="login" data-max_width="400" class="style1" href="/login">
                                Log in here.
                                </a>
                            </div>
                            </div>
                            <div class="grid-6">
                            <input type="submit" class="btn btn-medium btn-blue" value="Create Account" name="submit">
                            </div>
                            <div class="catchall"></div>
                        </div>
				</form>
            <?php }, 'widget.php', utils::isAjax());

        page::get()
            ->clearViews('print_messages')//we will print messages in the form area
            ->addView(
	            page::create()
	                ->addView('elements/section-centered.php')
	                ->addView(
	            function(){ ?>
	            <?php page::get()->renderViews('inner'); ?>
	            <?php }),
            'content-narrow');

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






}
