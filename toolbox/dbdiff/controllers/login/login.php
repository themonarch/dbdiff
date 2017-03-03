<?php
namespace toolbox;
class login_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member')
            ->requires('guest', function(){
            	utils::redirectTo();
            });

        sidebarV2::get('top_nav')->setActive('Log In');

		title::get()
		    ->addCrumb('Log In');

    }

	function isValid(){

			$user_id = user::checkPass(
	            isset($_POST['email']) ? $_POST['email']: null,
	            isset($_POST['password']) ? $_POST['password']: null
	        );

	       	if($user_id === false){
	            messages::setErrorMessage('Incorrect password or email!');
				return false;
	        }

	        //log the user in now.
	        user::login($user_id);

			user::generateEncryptionCookie($_POST['password']);

			return true;
	}


    function __construct(){

		if(utils::isPost()){
			if($this->isValid()){
	        	utils::redirectTo();
			}else{
				formV2::storeValues($_POST);
			}
		}

        widgetHelper::create()
            ->setHook('inner')
			->set('title', 'Log In')
			->set('style', 'max-width: 420px; margin: 0 auto;')
			->set('class', 'style3')
            ->add(function($tpl){ ?>
				<form class="form padding" method="post" action="/login"
					data-ajax_form="#<?php echo $tpl->widget_id; ?>">
						<?php
						messages::printMessages('messages', 'style5');
				        formV2::textField()
				        	->setTypeText()
							->setLabel('Email')
				        	->setName('email')
				        	->renderViews();
				        ?>
				        <div class="catchall spacer"></div>
				        <?php
				        formV2::textField()
				        	->setTypePassword()
							->setLabel('Password')
				        	->setName('password')
				        	->renderViews();
						?>
				        <div class="catchall spacer-2"></div>
						<div style="text-align: right; margin: 15px 0px 0;">
							<a style="float: left;" href="/login/forgot" class="btn btn-medium">Forgot password?</a>
							<input type="submit" class="btn btn-medium btn-blue" value="Login" name="submit">
						</div>
				        <div class="catchall"></div>
				        <div class="form_link">
				            Don't have an account? <a class="style1" data-overlay-id="signup"
				            	data-max_width="420" href="/signup"> Sign up here. </a>
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

}
