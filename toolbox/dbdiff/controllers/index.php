<?php
namespace toolbox;
class index_controller {

    static function setup(){

		sidebarV2::get('top_nav')->setActive('Home');

        accessControl::get()//no login required for the homepage
            ->removeRequired('member');

    }

	function setDemoConnectionPostData(){
		if($this->user->hasCustomValue('demo_db_pass')){
			$demo_db_pass = $this->user->getCustomValue('demo_db_pass');
		}else{
			$demo_db_pass = utils::getRandomString(8);
			$this->user->setCustomValue('demo_db_pass', $demo_db_pass);
		}

		//host
		$_POST['Host']['quick_connect-0'] = 'demo.'.config::getSetting('HTTP_HOST');
		$_POST['Host']['quick_connect-1'] = 'demo.'.config::getSetting('HTTP_HOST');


		//user
		$_POST['User']['quick_connect-0'] = 'demo_'.$this->user->getStringID();
		$_POST['User']['quick_connect-1'] = 'demo_'.$this->user->getStringID();

		//pass
		$_POST['Password']['quick_connect-0'] = $demo_db_pass;
		$_POST['Password']['quick_connect-1'] = $demo_db_pass;

		//port
		$_POST['Port']['quick_connect-0'] = '3306';
		$_POST['Port']['quick_connect-1'] = '3306';

		//database
		$_POST['Database']['quick_connect-0'] = 'dbdiff-demos-1';
		$_POST['Database']['quick_connect-1'] = 'dbdiff-demos-2';
	}

    function __construct(){

		messages::readMessages('quick_connect-0');
		messages::readMessages('quick_connect-1');

		title::get()//add a descriptive title for the homepage
		    ->addCrumb('A Quick Schema Comparison & Sync Tool for MySQL Databases');

		//add a meta description
		page::get()->addView(function(){ ?>
<meta name="description" content="A free-to-use web-based app to visually compare
and generate alter SQL to syncronize your MySQL databases.">
		<?php }, 'start_of_head_tag');

	    page::get()->set('demo', false);

		if(//if clicked the demo toggle
			utils::isPost()
			&& isset($_POST['dynamic_form_submit'])
			&& $_POST['dynamic_form_submit'] == 'demo'
		){

		    page::get()->set('demo', true);

			//create user if not exists
			$user = $this->getUser();

			//if demo db already exists
			if($user->hasCustomValue('demo_sync_id')){
				//get existing demo connection
				$sync = sync::get($user->getCustomValue('demo_sync_id'));

			}else{
				$this->setDemoConnectionPostData();
			}

			formV2::storeValues();



		}elseif(//submitting main form (quick connect) for a DEMO
            utils::isPost()
            && !(isset($_POST['dynamic_form_submit'])
            && $_POST['dynamic_form_submit'] == 'live')//not switching to live form
            && isset($_POST['widget_unique_id'])
            && $_POST['widget_unique_id'] == 'quick_diff'
            && isset($_POST['demo'])
            && $_POST['demo'] === 'true'
			&& ($profile_id = $this->processQuickConnectDemoForm())
        ){
			//new profile created successfully ... go to the comparison
			utils::redirectTo('/compare/'.$profile_id);

		}elseif(//submitting main form (quick connect)
            utils::isPost()
            && !(isset($_POST['dynamic_form_submit']) && $_POST['dynamic_form_submit'] == 'live')//not switching to live form
            && isset($_POST['widget_unique_id'])
            && $_POST['widget_unique_id'] == 'quick_diff'
            && $this->isValidQuickConnectForm()
			&& ($profile_id = $this->processQuickConnectForm())
        ){
			//new profile created successfully ... go to the comparison
			utils::redirectTo('/compare/'.$profile_id);

        }elseif(//choosing a db
            utils::isPost()
            && isset($_POST['choose_db'])
		){
			$_POST['display_name-Database'] = $_POST['Database'];
			formV2::storeValues();

        }elseif(//clicked on 'choose database' button
            utils::isPost()
            && isset($_POST['submit'])
            && $this->isValidDatabase()//validates form and connects to the database
		){
            if($_POST['submit'] === 'Database[quick_connect-0]'){
                $index = 'quick_connect-0';
            }else{
                $index = 'quick_connect-1';
            }


			//create a list of databases of databases from the submitted connection
			widgetHelper::create()
				->set('name', 'Database['.$index.']')
                ->set('connection_id', 'dbsync-'.$index)
				->setHook('database_table')
				->add(function($tpl){
					$_POST['widget_unique_id'] = $tpl->widget_unique_id;
					datatableV2::create()
						->setPaginationLimit(1)
						->setLimit(5)
						->set('container_class', 'style1')
						->set('name', $tpl->name)
						->enableSearch(1, false)
						->setSortInline()
					    ->setSelect('*,
					        "N/A" as `tables`,
					        "N/A" as `size`')
					    ->setFrom('`information_schema`.`SCHEMATA`')
					    ->set('db', $tpl->connection_id)
					    ->set('post_data', urlencode(json_encode($_POST)))
					    ->defineCol('SCHEMA_NAME', 'Database')
					    ->defineCol('SCHEMA_NAME', 'Actions', function($val, $rows, $dt){ ?>
					            <button type="submit" name="<?php echo $dt->name; ?>"
					                    class="btn btn-small btn-blue" value=<?php
					                    echo db::quote($val); ?>>Choose Database</button>
					    <?php })
						->addView(function(){ ?>
					    <div class="catchall spacer-2"></div>
						<?php }, 'pre-table')
						->addView(function(){ ?>
					    <div class="catchall spacer-2"></div>
						<?php }, 'post-table')
					    ->renderViews();

				}, 'blank.php', 'database_list');




		    //render form containing the list of databases
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
</form> <?php
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


		//if no active session
		//show demo toggle
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

		//Main heading contents
		page::get()->addView(function(){ ?>
			<div style="text-align: center; font-size: 40px; text-transform: uppercase; color: #787878; font-weight: 900; margin-bottom: 1%;">
			Compare MySQL Databases for Differences<br>
			Syncronize Diverged Schemas with <span class="text-important">a Click</span>
			</div>
			<div class="catchall spacer-3"></div>
			<div style="text-align: center; max-width: 800px; margin: 0 auto;">
				<p>Find schema differences in your databases easily, no signup or email required! Just use the form below.</p>

				<div class="catchall spacer-4"></div>
			</div>
		<?php }, 'content-narrow');

		//main container for quick connect templates
        widgetHelper::create()
            ->set('title', 'Quick Diff')
            ->set('class', 'style3')
            ->add('dbdiff/quick_diff.php', 'widget-reload.php', 'quick_diff');

		//some spacing after the quick connect form
		page::get()->addView(function(){ ?>
			<div class="catchall spacer-2"></div>
			<div class="catchall spacer-5"></div>
<div class="header-line style2">
    <div class="inner">Recent Diffs</div>
    <div class="gradient-line"></div>
</div>
<div class="catchall spacer-1"></div>
		<?php }, 'content-narrow');

		// recent comparisons widget
		widgetHelper::create()
            ->set('class', 'style4')
			->set('title', 'My Recent Comparisons')
			->add('dbdiff/recent_comparisons.php', 'widget-reload.php', 'recent_comparisons');

    }

	function isValidQuickConnectForm(){
        validator::validate('Host', 'general');
        validator::validate('User', 'general');
        //validator::validate('Password', 'general');
        validator::validate('Database', 'general');
        validator::validate('Port', function($val){
			if(!is_numeric($val)){
				return "Please enter numbers only!";
			}

			return true;

        });


		return validator::isValid();
	}

	private $user;
	function getUser(){
		if($this->user !== null){
			return $this->user;
		}

		//first set up a session if not already exists
		$user = null;

		//if not logged in and no guest account...
		if(!user::isUserLoggedIn() && !user::isGuestLoggedIn()){
			user::create(//create guest account
				array('password' => utils::getRandomString(10)),//with a random password
				true, //log current user into guest account
				true //generate an excryption cookie
			);

            $user = new user(user::$last_created_user_id);

		}else{//user already logged in as member or guest
			$user = user::getUserLoggedIn();
		}
		$this->user = $user;
		return $user;
		return new user();
	}

	function processQuickConnectForm(){
		$this->getUser();

		//save connection #1
		$conn_id_1 = $this->createConnection(
			$_POST['Host']['quick_connect-0'],
			$_POST['User']['quick_connect-0'],
			$_POST['Password']['quick_connect-0'],
			$_POST['Port']['quick_connect-0']
		);

		//save connection #2
		$conn_id_2 = $this->createConnection(
			$_POST['Host']['quick_connect-1'],
			$_POST['User']['quick_connect-1'],
			$_POST['Password']['quick_connect-1'],
			$_POST['Port']['quick_connect-1']
		);

		//save comparison
		$compare_id = $this->createComparison($conn_id_1, $conn_id_2,
			$_POST['Database']['quick_connect-0'],
			$_POST['Database']['quick_connect-1']
		);

		return $compare_id;

	}

	function processQuickConnectDemoForm(){
		$user = $this->getUser();

		$demo_db_pass = $user->getCustomValue('demo_db_pass');

		//create the table(s) on db1
		db::query("DROP TABLE IF EXISTS `dbdiff-demos-1`.`wp_posts-".$user->getStringID()."`");
		db::query("CREATE TABLE `dbdiff-demos-1`.`wp_posts-".$user->getStringID()."` (
			`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`post_author` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			`post_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`post_date_gmt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`post_content` LONGTEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
			`post_title` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
			`post_excerpt` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
			`post_status` ENUM('publish','draft','trash') NOT NULL DEFAULT 'publish' COLLATE 'utf8mb4_unicode_520_ci',
			`comment_status` VARCHAR(20) NOT NULL DEFAULT 'open' COLLATE 'utf8mb4_unicode_520_ci',
			`ping_status` VARCHAR(20) NOT NULL DEFAULT 'open' COLLATE 'utf8mb4_unicode_520_ci',
			`post_password` VARCHAR(20) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
			`post_name` VARCHAR(100) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
			`to_ping` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
			`pinged` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
			`post_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`post_modified_gmt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			`post_content_filtered` LONGTEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
			`post_parent` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			`guid` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
			`menu_order` INT(11) NOT NULL DEFAULT '0',
			`post_type` VARCHAR(20) NOT NULL DEFAULT 'post' COLLATE 'utf8mb4_unicode_520_ci',
			`post_mime_type` VARCHAR(200) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
			`comment_count` BIGINT(20) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			UNIQUE INDEX `post_name` (`post_name`),
			INDEX `post_parent` (`post_parent`),
			INDEX `post_author` (`post_author`),
			INDEX `type_status_date` (`post_type`, `post_date`, `id`)
		)
		COLLATE='utf8mb4_unicode_520_ci'
		ENGINE=InnoDB
		");



		//create the table(s) on db2
		db::query("DROP TABLE IF EXISTS `dbdiff-demos-2`.`wp_posts-".$user->getStringID()."`");
		db::query("CREATE TABLE `dbdiff-demos-2`.`wp_posts-".$user->getStringID()."` (
					`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
					`post_author` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
					`post_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					`post_date_gmt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					`post_content` LONGTEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
					`post_title` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
					`post_excerpt` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
					`post_status` VARCHAR(20) NOT NULL DEFAULT 'publish' COLLATE 'utf8mb4_unicode_520_ci',
					`comment_status` VARCHAR(20) NOT NULL DEFAULT 'open' COLLATE 'utf8mb4_unicode_520_ci',
					`ping_status` VARCHAR(20) NOT NULL DEFAULT 'open' COLLATE 'utf8mb4_unicode_520_ci',
					`post_password` VARCHAR(20) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
					`post_name` VARCHAR(200) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
					`to_ping` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
					`pinged` TEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
					`post_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					`post_modified_gmt` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
					`post_content_filtered` LONGTEXT NOT NULL COLLATE 'utf8mb4_unicode_520_ci',
					`post_parent` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
					`guid` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
					`menu_order` INT(11) NOT NULL DEFAULT '0',
					`post_type` VARCHAR(20) NOT NULL DEFAULT 'post' COLLATE 'utf8mb4_unicode_520_ci',
					`post_mime_type` VARCHAR(100) NOT NULL DEFAULT '' COLLATE 'utf8mb4_unicode_520_ci',
					`comment_count` BIGINT(20) NOT NULL DEFAULT '0',
					PRIMARY KEY (`ID`),
					INDEX `type_status_date` (`post_type`, `post_date`, `ID`),
					INDEX `post_parent` (`post_parent`),
					INDEX `post_author` (`post_author`)
				)
				COLLATE='utf8mb4_unicode_520_ci'
				ENGINE=InnoDB
				AUTO_INCREMENT=4");

		//create user on db1 with access to table(s)
		db::query("GRANT USAGE ON *.* TO 'demo_".$user->getStringID()."'@'localhost'");
		db::query("DROP USER 'demo_".$user->getStringID()."'@'localhost'");
		db::query("CREATE USER 'demo_".$user->getStringID()."'@'localhost' IDENTIFIED BY '".$demo_db_pass."'");
		db::query("GRANT USAGE ON *.* TO 'demo_".$user->getStringID()."'@'localhost'");
		db::query("GRANT SELECT, ALTER, DELETE, DROP,
		INDEX, INSERT, REFERENCES, UPDATE, CREATE,
		SHOW VIEW, CREATE VIEW  ON TABLE `dbdiff-demos-1`.`wp_posts-"
		.$user->getStringID()."` TO 'demo_".$user->getStringID()."'@'localhost'");

		//create user on db2 with access to table(s)
		//db::query("CREATE USER 'demo_".$user->getStringID()."'@'localhost' IDENTIFIED BY ''");
		//db::query("GRANT USAGE ON *.* TO 'demo_".$user->getStringID()."'@'localhost'");
		db::query("GRANT SELECT, ALTER, DELETE, DROP,
		INDEX, INSERT, REFERENCES, UPDATE, CREATE,
		SHOW VIEW, CREATE VIEW  ON TABLE `dbdiff-demos-2`.`wp_posts-"
		.$user->getStringID()."` TO 'demo_".$user->getStringID()."'@'localhost'");

		$this->setDemoConnectionPostData();

		//save connection #1
		$conn_id_1 = $this->createConnection(
			$_POST['Host']['quick_connect-0'],
			$_POST['User']['quick_connect-0'],
			$_POST['Password']['quick_connect-0'],
			$_POST['Port']['quick_connect-0'],
			$_POST['Database']['quick_connect-0']
		);

		//save connection #2
		$conn_id_2 = $this->createConnection(
			$_POST['Host']['quick_connect-1'],
			$_POST['User']['quick_connect-1'],
			$_POST['Password']['quick_connect-1'],
			$_POST['Port']['quick_connect-1'],
			$_POST['Database']['quick_connect-1']
		);

		//save comparison
		$compare_id = $this->createComparison($conn_id_1, $conn_id_2,
			$_POST['Database']['quick_connect-0'],
			$_POST['Database']['quick_connect-1']
		);

		return $compare_id;

	}

	function isValidDatabase(){
		if($_POST['submit'] === 'Database[quick_connect-0]'){
			$index = 'quick_connect-0';
		}else{
			$index = 'quick_connect-1';
		}

		if(isset($_POST['Host'][$index] )){
			$_POST['Host'][$index] = utils::removeStringFromBeginning($_POST['Host'][$index] , 'https://');
			$_POST['Host'][$index]  = utils::removeStringFromBeginning($_POST['Host'][$index] , 'http://');
			$_POST['Host'][$index]  = utils::removeStringFromEnd($_POST['Host'][$index] , '/');
		}

        validator::validate('Host['.$index.']', function($val){
        	if(trim($val) === ''){
        		return "Please enter a domain or IP";
        	}
        	try{
        		$url_parts = utils::parseUrl($val);
				if($url_parts['domain'] !== $val){
					//return 'Please enter a domain only without http protocol or slashes.';
				}

        	}catch(toolboxException $e){}
        	return true;
        });
        validator::validate('User['.$index.']', 'general');
        //validator::validate('Password['.$index.']', 'general');
        validator::validate('Port['.$index.']', function($val){
			if(!is_numeric($val)){
				return "Please enter numbers only!";
			}

			return true;

        });

		if(!validator::isValid()){
			return false;
		}
		try{
			//attempt to connect
	        db::connect(
	            $_POST['Host'][$index],
	            $_POST['User'][$index],
	            $_POST['Password'][$index],
	            '',
	            'dbsync-'.$index,
                $_POST['Port'][$index]
	        );
	        db::setDB();
			return true;
		}catch(toolboxException $e){
			//show error msg
			messages::setErrorMessage($e->getMessage(), $index);
        	formV2::storeValues();
			return false;
		}
	}


	/**
	 * attempts creating a new connection,
	 * returns false on fail, with error msg already set
	 */
	function createConnection($host, $user, $pass, $port){
		$row = db::query('select `connection_id` from `db_connections`
			 where `user_id` = '.db::quote(user::getUserLoggedIn()->getID()).'
			 and `host` = '.db::quote($host).'
			 and `user` = '.db::quote($user).'
			 and `Password` = '.db::quote(user::getUserLoggedIn()->encrypt($pass)).'
			 and `Port` = '.db::quote($port).'
			 limit 1
		')->fetchRow();
		if($row !== null){
			return $row->connection_id;
		}

		//TODO try multiple times incase of primary id collision
		$db_id = 'udb-'.utils::getRandomString(5);
		db::query('INSERT INTO `db_connections` (
					`connection_id`,
					`user_id`,
					`name`,
					`host`,
					`user`,
					`password`,
					`port`
				) VALUES (
					'.db::quote($db_id).',
					'.db::quote(user::getUserLoggedIn()->getID()).',
					'.db::quote(null)
					.', '.db::quote($host)
					.', '.db::quote($user)
					.', '.db::quote(user::getUserLoggedIn()->encrypt($pass))
					.', '.db::quote($port)
				.')');


		return $db_id;
	}

	function createComparison($conn1, $conn2, $table1, $table2){
		//if already exists
		$row = db::query('select * from `db_sync_profiles`
		where `user_id` = '.db::quote(user::getUserLoggedIn()->getID()).'
		and `target_conn_id` = '.db::quote($conn2).'
		and `target_db` = '.db::quote($table2).'
		and `source_conn_id` = '.db::quote($conn1).'
		and `source_db` = '.db::quote($table1).'
		')->fetchRow();

		if($row !== null){
			return $row->id;
		}


		$sync_id = utils::getRandomString();

		db::query('insert into `db_sync_profiles` (
			`id`,
			`user_id`,
			`target_conn_id`,
			`target_db`,
			`source_conn_id`,
			`source_db`,
			`sync_direction`,
			`description`,
			`last_viewed`
		) VALUES (
			'.db::quote($sync_id).',
			'.db::quote(user::getUserLoggedIn()->getID()).',
			'.db::quote($conn2).',
			'.db::quote($table2).',
			'.db::quote($conn1).',
			'.db::quote($table1).',
			"both",
			"",
			now()
		)');

		return $sync_id;


	}


	static function passThru(){
		page::get()->addView(function(){ ?>
	        <link rel="stylesheet" type="text/css" href="/assets/app/css/db.css">
			<script src="/assets/app/ace/src-min-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>
		<?php }, 'end_of_head_tag');
	}

}