<?php
namespace toolbox;
class homepage_internal {

    static function setup(){

		sidebarV2::get('top_nav')->setActive('Home');

    }

    function __construct(){

		messages::readMessages('quick_connect-0');
		messages::readMessages('quick_connect-1');

		title::get()//add a descriptive title for the public homepage
		    ->addCrumb('A Schema Comparison & Sync Tool for MySQL Databases');

		//add a meta description
		page::get()->addView(function(){ ?>
<meta name="description" content="A free-to-use web-based app to visually compare
and generate alter SQL to syncronize your MySQL databases.">
		<?php }, 'start_of_head_tag');

	    page::get()->set('demo', false);


		/**
		 * There are A LOT of form actions on the page,
		 * below we determine which action user is taking
		 */

		if(//if clicked the demo toggle
			utils::isPost()
			&& isset($_POST['dynamic_form_submit'])
			&& $_POST['dynamic_form_submit'] == 'demo'
		){//set up the demo form data


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


		//add a demo toggle
		page::get()
    		->addView(function($tpl){ ?>
			<div class="catchall"></div>
			<div style="float: right; max-width: 120px; margin: 7px 7px 0px;" class="switches style1">
				<div class="container">
					<span class="switch <?php if(!$tpl->demo) echo 'active'; ?>" data-dynamic_form_submit="live">
						Live</span>
					<span class="switch <?php if($tpl->demo) echo 'active'; ?>" data-dynamic_form_submit="demo">
						Demo</span>
				</div>
			</div>
			<div class="catchall"></div>
			<?php }, 'quick_diff-header');

		//Main heading contents, these are templates wrapped in functions for
		//ease of development, but can easily be dropped into an homepage_contents.html
		//in case a designer needs to work with them.
		page::get()->addView(function(){ ?>
			<div style="text-align: center; font-size: 40px;
			text-transform: uppercase;
			color: #222222; font-weight: 900;
			margin-bottom: 1%;">
			Check MySQL Databases for Differences<br>
			Syncronize Schemas with a Click
			</div>
			<div class="catchall spacer-3"></div>
			<div class="catchall spacer-4"></div>
			<div style="text-align: center; max-width: 850px; margin: 0 auto;">

            <div class="header-line style4">
                <div class="inner">Features</div>
                <div class="gradient-line"></div>
            </div>
			<div class="catchall spacer-2"></div>
            <div class="grid-6 grid-s-12">
            <ul class="list">
                <li><b>Compare Databases</b> - Visually see mismatching or missing tables between two databases across any servers.</li>
                <li><b>Schema Diff</b> - View side by side schema comparison for each table so you can see what's different in a glance.</li>
                <li><b>Migration Script Generator</b> - Automatically generate SQL alter scripts for both target or source servers.</li>
                <li><b>Execute Migration Scripts</b> - Run your migration scripts for either target or source database ,
                    with options to pick and choose individual changes to sync.</li>
            </ul>
            </div>
            <div class="grid-6 grid-s-12">
            <ul class="list">
                <li><b>Migration Status & History</b> - View all migration scripts you ran, see status of long running alters with
                    option to kill already running migrations.</li>
                <li><b>Localhost Connect</b> - Connect to a server running on your local machine (using thrid-party software),
                    even if you are behind a NAT or firewall</li>
                <li><b>Save Comparisons</b> - All your comparisons are saved to your account with encryption so you can
                    quickly and securely re-run your comparisons at any time.
                    </li>
            </ul>
            </div>
            <div class="catchall spacer-4"></div>
            <div class="catchall spacer-4"></div>
			<p><b>Try it now!</b><?php
				if(!user::isMemberLoggedIn()){ ?> No signup or email required.<?php } ?></p>
            <div class="catchall spacer-1"></div>
			<i class="icon-down-open-big"></i>

			<div class="catchall spacer-3"></div>
			</div>
		<?php }, 'pre-pre-content');

		//main container for quick connect forms
        widgetHelper::create()
            ->add('dbdiff/quick_diff.php', 'panel.php', 'quick_diff');

		//some spacing after the quick connect form for a changelog widget
		page::get()->addView(function(){ ?>
<div class="catchall spacer-5"></div>
<div class="catchall spacer-2"></div>
<div class="header-line style2">
    <div class="inner">Changelog</div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-2"></div>
		<?php }, 'content-narrow');


		//create a table widget that displays data from the changelog table
		widgetHelper::create()
			->set('title', 'Changelog / Recent Updates')
			->add(function($tpl){
				datatableV2::create()
					->enableSearch(2, false)
					->enableSort(0, false)
					->setSort(2, 'desc')
					->set('widget_id', $tpl->widget_id)
					->setPaginationDestination('#'.$tpl->widget_id)
					->setColSetting(0, 'style-td', 'white-space: pre-line;')
					->setColSetting(2, 'style', 'width: 300px;')
				    ->setTableClass('style4')
					->setColSetting(1, 'style', 'width: 300px;')
					->defineCol('message', 'Description', function($val){
						return utils::htmlEncode($val);
					})
					->defineCol('author', 'Commit Author', function($val){
						$val = explode(' <', $val);
						return utils::htmlEncode($val[0]);
					})
					->defineCol('date', 'Date', function($val){ ?>
						<span class="timeago" title="<?php echo $val; ?>+0000"><?php echo $val; ?></span>
					<?php })
					->setSortInline()
					->setSelect('*')
				    ->setFrom('changelog')
				    ->renderViews();
			}, 'widget-reload.php', utils::isAjax());


    }

}