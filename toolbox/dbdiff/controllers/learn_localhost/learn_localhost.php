<?php
namespace toolbox;
class learn_localhost_controller {

    static function setup(){

        accessControl::get()
            ->removeRequired('member');


		title::get()
		    ->addCrumb('Learn How to Connect to Localhost Over the Internet');

    }



    function __construct(){


        widgetHelper::create()
            ->setHook('inner')
			->set('title', 'How to Connect to Your Localhost')
			->set('style', 'max-width: 620px; margin: 0 auto;')
			->set('class', 'style3')
            ->add(function($tpl){ ?>
            	<div class="form_panel">
<p>
To connect to your local running MySQL database, you must use a tunnel.
There are many ways to create a localhost tunnel, but the easiest way is
to use a third party service. We recommend using <a target="_blank" href="https://ngrok.com/">ngrok</a>.
It is easy to use, supports most OS's and is free!
</p>



</div>           <?php }, 'widget.php', utils::isAjax());

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
