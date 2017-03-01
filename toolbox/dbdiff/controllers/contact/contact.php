<?php
namespace toolbox;
class contact_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member');

        sidebarV2::get('top_nav')->setActive('Contact');

		title::get()
            ->setSubtitle('Contact the Developer')
		    ->addCrumb('Contact');

    }

	function isValid(){
		validator::validate('email');
		validator::validate('subject', 'general');
		validator::validate('body', 'general');
		return validator::isValid();
	}


    function __construct(){

        page::get()
            ->clearViews('print_messages');//we will print messages in the form area

		if(utils::isPost()){
			if($this->isValid()){

				messages::setSuccessMessage('Your message has been received, thanks!');
				appUtils::sendEmail('contact@dbdiff.com', $_POST['subject'], $_POST['body']);


				return page::get()
					->addView(function(){ ?>
						<div class="catchall spacer-2"></div>
						<?php
						messages::printMessages('messages', 'style5');
						?>
						<div class="catchall spacer-2"></div>
						<div style="text-align: center;">
							<a class="btn btn-medium btn-link" href="/">Return to Homepage</a>
						</div>
						<?php
					}, 'content-narrow');
			}else{
				formV2::storeValues($_POST);
			}
		}


		page::get()->addView(function($tpl){ ?>
				<form  style="max-width: 600px; margin: 0 auto;" class="form" method="post" action="/contact">
                    <div class="catchall spacer-1"></div>
				    <div style="text-align: center;">
				    <p>Contact me with your feedback, requests, questions, marketing, or anything else.</p>
				    </div>
                    <div class="form_panel padding">
						<?php
						messages::printMessages('messages', 'style5');
				        formV2::textField()
				        	->setTypeText()
							->setLabel('Your Email Address')
				        	->setName('email')
				        	->renderViews();
				        	?>
                    <div class="catchall spacer-1"></div>
			        	<?php
				        formV2::textField()
				        	->setTypeText()
							->setLabel('Subject')
				        	->setName('subject')
				        	->renderViews();
				        ?>
                    <div class="catchall spacer-1"></div>
                        <?php
                        formV2::textarea()
                            ->setLabel('What can I do for you?')
                            ->setName('body')
                            ->renderViews();
                        ?>
				        <div class="catchall"></div>
			        </div>
                	<div style="max-width: 400px; margin: 0 auto; text-align: center;">
                		<input type="submit" class="btn btn-medium btn-full_width btn-blue" value="Send!" name="submit">
                	</div>
				</form>
            <?php }, 'content-narrow');

    }

}
