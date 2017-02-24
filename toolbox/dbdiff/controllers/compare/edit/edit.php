<?php
namespace toolbox;
class edit_controller {

    function __construct(){


        //initializations
        title::get()->addCrumb('Edit');
        $profile_id = router::get()->getParam('profile_id');
        $sync = sync::get($profile_id);

		//clear the connection msgs here so they don't show again (we display them on this page)
		messages::readMessages('quick_connect-0');
		messages::readMessages('quick_connect-1');

		if(//submitting main form
            utils::isPost()
            && utils::postHas('widget_unique_id', 'quick_diff')
            && $this->isValidQuickConnectForm()
        ){

		//update the connection #1
		$conn_id_1 = $this->createConnection(
			$_POST['Host']['quick_connect-0'],
			$_POST['User']['quick_connect-0'],
			$_POST['Password']['quick_connect-0'],
			$_POST['Port']['quick_connect-0']
		);

		//update the connection #2
		$conn_id_2 = $this->createConnection(
			$_POST['Host']['quick_connect-1'],
			$_POST['User']['quick_connect-1'],
			$_POST['Password']['quick_connect-1'],
			$_POST['Port']['quick_connect-1']
		);

		//update the sync profile
		$sync->updateSourceConnection($conn_id_1, $_POST['Database']['quick_connect-0']);
		$sync->updateTargetConnection($conn_id_2, $_POST['Database']['quick_connect-1']);



			//changes saved successfully, go to comparison
			utils::redirectTo('/compare/'.$profile_id);

        }elseif(//choose a db
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
				->add('dbdiff/database_list.php', 'blank.php', 'database_list');


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

		//resture connection details if submitted by user
        if(messages::readMessages('form', 'Host') === null){

			//host
			$_POST['Host']['quick_connect-0'] = $sync->getSourceConnection()->getHost();
			$_POST['Host']['quick_connect-1'] = $sync->getTargetConnection()->getHost();


			//user
			$_POST['User']['quick_connect-0'] = $sync->getSourceConnection()->getUser();
			$_POST['User']['quick_connect-1'] = $sync->getTargetConnection()->getUser();

			//pass
			$_POST['Password']['quick_connect-0'] = $sync->getSourceConnection()->getPass();
			$_POST['Password']['quick_connect-1'] = $sync->getTargetConnection()->getPass();

			//port
			$_POST['Port']['quick_connect-0'] = $sync->getSourceConnection()->getPort();
			$_POST['Port']['quick_connect-1'] = $sync->getTargetConnection()->getPort();

			//database
			$_POST['Database']['quick_connect-0'] = $sync->getSourceDB();
			$_POST['Database']['quick_connect-1'] = $sync->getTargetDB();

			//recreate the connection forms
			formV2::storeValues();

        }



		page::get()
			->set('demo', false)
    		->addView(function($tpl){ ?>
<div class="catchall"></div>
<div class="catchall spacer-1"></div>
		<?php }, 'quick_diff-header');

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


		//main container for quick connect templates
        widgetHelper::create()
            ->set('title', 'Edit Diff')
            ->set('class', 'style3')
            ->add('dbdiff/quick_diff.php', 'widget-reload.php', utils::isAjax());



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

		if(validator::isValid()){
			foreach(array('quick_connect-0', 'quick_connect-1') as $index){
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
				}catch(toolboxException $e){
					//show error msg
					messages::setErrorMessage($e->getMessage(), $index);
					formV2::storeValues();
					return false;
				}
			}
		}

		return validator::isValid();
	}

}
