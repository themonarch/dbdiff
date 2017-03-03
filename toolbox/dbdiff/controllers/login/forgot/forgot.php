<?php
namespace toolbox;
class forgot_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member')
            ->requires('guest', function(){
            	utils::redirectTo();
            });

		title::get()
		    ->addCrumb('Password Recovery');

    }

	function isValid(){
		$user = null;
        //validate email exists
        try{
            $user = user::getByEmail($_POST['email']);
        }catch(userNotFound $e){
        	messages::setErrorMessage('That account was not found.');
            return false;
        }


        try{//send recovery email
            $user->sendPasswordResetEmail();
        }catch(softPublicException $e){
            //can't send
            messages::setErrorMessage($e->getMessage());
            return false;
        }

        //success
        messages::setSuccessMessage('An email has been sent to <b>'
        	.htmlspecialchars($user->getEmail(), ENT_QUOTES).'</b> with instructions on how to reset the password.
        	   Remember to also check in your spam folder.');

		return true;
	}


    function __construct(){


        page::get()//set up the main container
            ->clearViews('print_messages')//we will print messages in the form area
            ->clearViews('top_spacing')
            ->addView(
	            page::create()
	                ->addView('elements/section-centered.php')
	                ->addView(
	            function(){ ?>
	            <?php page::get()->renderViews('inner'); ?>
	            <?php }),
            'content-narrow');

		//set up our widget
		$widget = widgetHelper::create()
		            ->setHook('inner')
					->set('title', 'Forgot Password')
					->set('style', 'max-width: 500px; margin: 0 auto;')
					->set('class', 'style3');

		if(utils::isPost()){
			if($this->isValid()){
				//on success, we don't render the form, just print the success msg
	            $widget->add(function($tpl){ ?>
	            	<div class="form_panel">
	            	<?php messages::printMessages('messages', 'style5'); ?>
	            	<div class="catchall spacer-2"></div>
	            	<div style="text-align: center;">
						<a class="btn btn-link btn-medium" href="/login">Back to Login</a>
	            	</div>
            		</div>
				<?php }, 'widget.php', true);

			}else{
				formV2::storeValues($_POST);
			}
		}

		//password recovery form
        $widget->add(function($tpl){ ?>
        	<div class="form_panel">
			<form class="form" method="post" action="/login/forgot"
				data-ajax_form="#<?php echo $tpl->widget_id; ?>">
					<?php
					messages::printMessages('messages', 'style5');
					messages::output('Submit the email address associated with your account and we will send you
            				an email containing instructions on how to reset your password.
            				<br><br><b>WARNING:</b> Once your password is changed, some data on your account
            				<b>will be lost</b> because we encrypt sensitive data using your password
            				as part of your account\'s encryption key.', 'notice', 'style5');
                    form::textField()
                            ->setTypeText()
                            ->setName('email')
                            ->render();
                    ?>
                    <div style="text-align: right; margin: 15px 0px 0;">
                        <a style="float: left;" href="/login" class="btn btn-medium">&laquo; Back to Login</a>
                        <input type="submit" class="btn btn-medium btn-blue" value="Send Reset Instructions" name="submit">
                    </div>
			</form>
			</div>
		<?php }, 'widget.php', utils::isAjax());



    }

}
