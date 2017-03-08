<?php
namespace toolbox;
class dashboard_internal {

    static function setup(){

		sidebarV2::get('top_nav')->setActive('Dashboard');

    }

    function __construct(){

		messages::readMessages('quick_connect-0');
		messages::readMessages('quick_connect-1');

		title::get()
		    ->addCrumb('Dashboard')
			->setSubtitle('Dashboard');


	    page::get()->set('demo', false);

		if(//if clicked the demo toggle
			utils::isPost()
			&& isset($_POST['dynamic_form_submit'])
			&& $_POST['dynamic_form_submit'] == 'demo'
		){

		    page::get()->set('demo', true);

			//create user if not exists
			$user = appUtils::createUserIfNotLoggedIn();

			//if demo db already exists
			if($user->hasCustomValue('demo_sync_id')){
				//get existing demo connection
				$sync = sync::get($user->getCustomValue('demo_sync_id'));

			}else{
				appUtils::setDemoConnectionPostData();//add the demo connection details to the $_POST
			}

			formV2::storeValues();//restore the $_POST to the user's form



		}elseif(//submitting main form (quick connect) for a DEMO
            utils::isPost()
            && !(isset($_POST['dynamic_form_submit'])
            && $_POST['dynamic_form_submit'] == 'live')//not switching to live form
            && isset($_POST['widget_unique_id'])
            && $_POST['widget_unique_id'] == 'quick_diff'
            && isset($_POST['demo'])
            && $_POST['demo'] === 'true'
			&& ($profile_id = appUtils::processQuickConnectDemoForm())
        ){//new comparison profile created successfully ...

        	//go to the comparison
			utils::redirectTo('/compare/'.$profile_id);

		}elseif(//submitting main form (quick connect) with their own connections
            utils::isPost()
            && !(isset($_POST['dynamic_form_submit']) && $_POST['dynamic_form_submit'] == 'live')//not switching to live form
            && isset($_POST['widget_unique_id'])
            && $_POST['widget_unique_id'] == 'quick_diff'
            && appUtils::isValidQuickConnectForm()
			&& ($profile_id = appUtils::processQuickConnectForm())
        ){//new comparison profile created successfully ...

			//go to the comparison
			utils::redirectTo('/compare/'.$profile_id);

        }elseif(//user chose a db for a connection
            utils::isPost()
            && isset($_POST['choose_db'])
		){
			//if user didn't hit back button
			if(isset($_POST['Database'])){
				//set the chosen db
				$_POST['display_name-Database'] = $_POST['Database'];
			}

			formV2::storeValues();//restore the connection form data with their chosen db

        }elseif(//clicked on 'choose database' button
            utils::isPost()
            && isset($_POST['submit'])
            && appUtils::isValidDatabase()//validates form and connects to the database
		){
			//determine which connection they are choosing for
            if($_POST['submit'] === 'Database[quick_connect-0]'){
                $index = 'quick_connect-0';
            }else{
                $index = 'quick_connect-1';
            }


			//display a table of databases for the submitted connection
			widgetHelper::create()
				->set('name', 'Database['.$index.']')
                ->set('connection_id', 'dbsync-'.$index)
				->set('title', 'Choose a Database')
				->set('class', 'style5')
				//add a back button that cancels and restores the form
				->addView(function($tpl){ ?>
<div class="widget-header-controls left">
	<button class="btn btn-small btn-silver" type="submit"><i class="icon-angle-double-left"></i> Back</button>
</div>
				<?php }, 'header')
				->setHook('database_table')
				->add('dbdiff/database_list.php', 'widget.php', 'database_list');


		    //add a container for the table of databases that has a form element
		    //so when user choses a database, the form is submitted to restore
		    //the connection form with their chosen database.
		    widgetHelper::create()
                ->setHook('connection-'.$index)
                ->set('connection_id', 'dbsync-'.$index)
				->set('name', 'Database['.$index.']')
				->set('index', $index)
                ->add(function($tpl){ ?>
<form data-ajax_form="#<?php echo $tpl->widget_id; ?>"
    data-show_loader="#<?php echo $tpl->widget_id; ?>"
    class="form" method="post" action="">

	<input type="hidden" name="choose_db" value="true">
	<input type="hidden" name="<?php echo $tpl->index; ?>" value="true">
            	<?php
            	formV2::storeValues();
            	page::get()->renderViews('database_table');
            	?>
</form><?php
                }, 'minimal.php', true);

        }


		### connection form #1
		widgetHelper::create()
			->set('index', 'quick_connect-0')
			->setHook('connection-0')
			->add('dbdiff/create_connection.php', 'minimal.php',
				(isset($_POST['submit']) && $_POST['submit'] == 'Database[quick_connect-0]')
				|| (isset($_POST['quick_connect-0'])));

		### connection form #2
		widgetHelper::create()
            ->set('index', 'quick_connect-1')
			->setHook('connection-1')
			->add('dbdiff/create_connection.php', 'minimal.php',
				(isset($_POST['submit']) && $_POST['submit'] == 'Database[quick_connect-1]')
				|| (isset($_POST['quick_connect-1']) ));

		page::get()
    		->addView(function($tpl){ ?>
        <div class="catchall spacer-2"></div>
			<?php }, 'quick_diff-header');

		//some spacing after the quick connect form
		page::get()->addView(function(){ ?>
<div class="catchall spacer-2"></div>
		<?php }, 'content-narrow');

		// recent comparisons widget
		widgetHelper::create()
            ->set('class', 'style4')
			->set('title', 'My Recent Comparisons')
			->add('dbdiff/recent_comparisons.php', 'widget-reload.php', 'recent_comparisons');

		//some spacing after the quick connect form
		page::get()->addView(function(){ ?>
<div class="catchall spacer-5"></div>
<div class="header-line style4">
    <div class="inner">Create a New Diff</div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-2"></div>
		<?php }, 'content-narrow');


		//main container for quick connect templates
        widgetHelper::create()
            ->add('dbdiff/quick_diff.php', 'panel.php', 'quick_diff');

    }

}