<?php
namespace toolbox;
class download_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member');

        sidebarV2::get('top_nav')->setActive('Download');

		title::get()
		    ->addCrumb('Download');

    }

	function isValid(){
		validator::validate('email');
		return validator::isValid();
	}


    function __construct(){

		$widget = widgetHelper::create()
            ->setHook('inner')
			->set('title', 'Download Coming Soon')
			->set('style', 'max-width: 540px; margin: 0 auto;')
			->set('class', 'style3');

		if(utils::isPost()){
			if($this->isValid()){
				if(dataStore::get('db')->getValue($_POST['email'], 'newsletter') === 'true'){
					messages::setSuccessMessage('You\'ve already subscribed to the newsletter, thanks!');
				}else{
					messages::setSuccessMessage('You have successfully subscribed to the newsletter, thanks!');
					dataStore::get('db')->setValue($_POST['email'], 'true', 'newsletter');
				}
				$widget->add(function($tpl){ ?>
				<div class="form_panel padding">
					<?php messages::printMessages('messages', 'style5'); ?>
			        <div class="catchall"></div>
				<div style="text-align: center;">
					<a class="btn btn-medium btn-link" href="/">Return to Homepage</a>
				</div>
		        </div>
            <?php }, 'widget.php', utils::isAjax());
			}else{
				formV2::storeValues($_POST);
			}
		}

		messages::setCustomMessage('Downloading a self hosted version of DBDiff will be available
		in the coming months. Please sign up below to be the first to know when it becomes available.', 'messages', 'info');

		$widget->add(function($tpl){ ?>
				<form class="form" method="post" action="/download"
					data-ajax_form="#<?php echo $tpl->widget_id; ?>">
					<div class="form_panel padding">
						<?php
						messages::printMessages('messages', 'style5');
				        formV2::textField()
				        	->setTypeText()
							->setLabel('Email')
				        	->setName('email')
							->setNote('We will not share your email with any third parties.')
				        	->renderViews();
				        ?>
				        <div class="catchall"></div>
				        </div>
<div class="datatable ">
	<div class="datatable-info datatable-section">
	<div style="max-width: 400px; margin: 0 auto;">
		<input type="submit" class="btn btn-medium btn-blue" value="Notify Me" name="submit">
	</div>
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

}
