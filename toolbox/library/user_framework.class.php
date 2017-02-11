<?php
namespace toolbox;
class user_framework {
    protected $user_id;
    protected $user_data;
    public static $last_created_user_id;
    function __construct($user_id, $is_string_id = false){
        if(!db::isConnected('default')){
            throw new userNotFound("DB not available to find user = ".$user_id, 1);
        }
        if($is_string_id === true){
            $this->user_data = db::query('
                    SELECT *
                    FROM `users`
                    WHERE `user_string_id` = '. db::quote($user_id))
                ->fetchRow();
            if($this->user_data === null){
                throw new userNotFound("User was not found with string id = ".$user_id, 1);
            }
            $this->user_id = $this->user_data->user_id;
        }elseif($is_string_id == 'email'){
            $this->user_data = db::query('
                    SELECT *
                    FROM `users`
                    WHERE `email_address` = '. db::quote($user_id))
                ->fetchRow();
            if($this->user_data === null){
                throw new userNotFound("User was not found with email = ".$user_id, 1);
            }
            $this->user_id = $this->user_data->user_id;
        }else{
            $this->user_id = (string)$user_id;
            $this->user_data = db::query('
                    SELECT *
                    FROM `users`
                    WHERE `user_id` = '. (int)$this->user_id)
                ->fetchRow();
        }

        if($this->user_data === null){
            throw new userNotFound("User was not found with string id = ".$user_id, 1);
        }

        user_framework::$instances['user_id'][$this->user_id] = $this;
        user_framework::$instances['user_string_id'][$this->user_data->user_string_id] = $this;
    }
    static $user_id_logged_in = null;
    public static function getUserLoggedIn(){
        if(self::$user_id_logged_in !== null && self::$user_id_logged_in !== false){
            return user::get(self::$user_id_logged_in);
        }
        if(
            isset($_COOKIE['login'])
            && (($user_id = user::login2ID($_COOKIE['login'])) !== false)
        ){
            self::$user_id_logged_in = $user_id;
            return user::get($user_id);
        }

        self::$user_id_logged_in = false;

        throw new userNotLoggedIn("Current user is not logged in.", 1);

        return new user();
    }

    static function isUserLoggedIn(){
        if(self::$user_id_logged_in !== null && self::$user_id_logged_in !== false){
            return true;
        }elseif(self::$user_id_logged_in === false){
            return false;
        }

        try{
            user::getUserLoggedIn();
            return true;
        }catch(userNotLoggedIn $e){
            return false;
        }
    }

    public static function checkPassOld($email, $password){

        $query = db::query('SELECT `user_id` FROM `users`
                    WHERE `email_address` = '.db::quote($email).'
                    AND `password_hash` = '.db::quote(utils::hash($password)).' limit 1');
        if($query->rowCount() === 0){
            return false;
        }

        return $query->fetchRow()->user_id;
    }

    public static function checkPass($email, $password){
        $user_data = db::query('select `password_hash`, `user_id` from `users` where `email_address` = '
            .db::quote($email).' limit 1')->fetchRow();
        if($user_data === null){
            return false;
        }

        if(!password_verify($password, $user_data->password_hash)){
            return self::checkPassOld($email, $password);
            //return false;
        }

        return $user_data->user_id;
    }

    /**
     * create a new user.
	 * @var $create_encryption_cookie - Triple layer encryption cookie:
	 * A random ENCRYPTED password is stored in user's cookie,
	 * which is encrypted and decrypted using a random salt stored in the db unique to each user
	 * and secondary salt using a global key stored in the config file of the codebase.
	 */
    public static function create($data = array(), $log_in = false, $create_encryption_cookie = false){
        $user_id = false;

        $called_class = get_called_class();

        if(!isset($data['password'])){
            throw new toolboxException("Password is missing!");
        }
        if(!isset($data['email'])){
            $data['email'] = null;
        }


        //generate random string
        $user_string_id = user::getUnusedUserStringId();
        $sql = 'INSERT INTO `users` (
                                    `user_string_id`,
                                    `email_address`,
                                    `password_hash`,
                                    `ip`
                                )
                                VALUES (
                                    '.db::quote($user_string_id).',
                                    '.db::quote($data['email']).',
                                    '.db::quote(password_hash($data['password'], PASSWORD_DEFAULT)).',
                                    '.db::quote(inet_pton($_SERVER['REMOTE_ADDR'])).'
                                )
                        ON DUPLICATE KEY UPDATE `user_id`=LAST_INSERT_ID(`user_id`) ';
        $query = db::query($sql);

        //if insert succeeded
        if($query->rowCount() === 1){
            //get insert id
            $user_id = $query->last_insert_id();
            $called_class::$last_created_user_id = $user_id;
        }

        if($query->rowCount() === 0 || $user_id === false){
            $called_class::$last_created_user_id = $query->last_insert_id();
            throw new userException('An account with this email already exists!', 1);
        }

		if($log_in){
            //log the user in now.
            user::login(user::$last_created_user_id);
		}

		if($create_encryption_cookie){
			user::generateEncryptionCookie($data['password']);
		}

        return true;

    }

	static function generateEncryptionCookie($plaintext_password){
        	//generate a random salt and store in db with a unique id
        	$enc_token = utils::getRandomString(20);
        	$enc_salt = openssl_random_pseudo_bytes(20);
			db::query('insert into `encryption_salt` (`salt_id`, `salt`)
			values ('.db::quote($enc_token).', '.db::quote($enc_salt).')');

        	//generate a random password
        	$password = utils::getRandomString(20);

			//encrypt the password with the generated salt we stored in the db
			$enc_password = utils::encrypt($plaintext_password, $enc_salt, config::getSetting('encryption_salt'));

			//send the encrypted password to the user
	        $cookieHelper = new cookieHelper();
	        $cookieHelper->setCookieExpirationToDays(999);
	        $cookieHelper->setCookieName('encryption_token');
	        $cookieHelper->setCookieValue($enc_password.'|'.$enc_token);
	        $cookieHelper->sendCookieTouser();

			$_COOKIE['encryption_token'] = $enc_password.'|'.$enc_token;

			//TODO: encrypt a sample string and store in db as a way to validate decryption for user?
	}

	function getEncryptionKey(){
		if(!isset($_COOKIE['encryption_token']) || trim($_COOKIE['encryption_token']) == ''){
			try{
				cookieHelper::create()->destroyCookie('encryption_token');
				cookieHelper::create()->destroyCookie('login');
			}catch(toolboxException $e){}
			throw softPublicException('Your session was not found or it may have expired. Please log in to continue.');
		}

		return $_COOKIE['encryption_token'];
	}

	function encrypt($string){
		return utils::encrypt($string, $this->getEncryptionKey(), config::getSetting('encryption_salt'));
	}

	function decrypt($string){
		return utils::decrypt($string, $this->getEncryptionKey(), config::getSetting('encryption_salt'));
	}

    public static function createNewLoginToken($user_id){
        $login_token = utils::getRandomString(30);
        $query = db::insert(
            'user_login_tokens',
            array('user_id' => db::quote($user_id), 'login_token' => db::quote($login_token)),
            array('login_token' => db::quote($login_token))
        );
        if($query->rowCount() > 0){
            return $login_token;
        }

        throw new toolboxException('Tried to create login token for non-existing user_id: '.$user_id);
    }

    public static function login($user_id){
        if(headers_sent()){
            return false;
        }
        //find user
        $query = db::query('SELECT * FROM `users` WHERE `user_id` = '. db::quote($user_id).' limit 1');
        if($query->rowCount() === 0){
            throw new toolboxException('Tried to log a user in to non-existing user_id: '.$user_id);
        }
        $user_data = $query->fetchRow();
        //create login token
        $cookieHelper = new cookieHelper();
        $cookieHelper->setCookieExpirationToDays(999);
        $cookieHelper->setCookieName('login');
        $cookieHelper->setCookieValue(user::createNewLoginToken($user_id));
        $cookieHelper->sendCookieTouser();


        return user::get($user_id);

    }

    protected static function login2ID($login_cookie){


        //find login token for this user + token
        //TODO: see if performance can be optimized by using user_string_id in tokens table instead of joining with users table
        $query = db::query('SELECT * FROM `user_login_tokens`'
                            .' WHERE `login_token` = '.db::quote($login_cookie).' limit 1');

        //if no records return false
        if($query->rowCount() === 0){
            return false;
        }

        $row = $query->fetchRow();

        //set hook to track session logins
        page::get()->addView(
            page::create()
                ->set('row', $row)
                ->addView('hooks/user_athenticated.php'), 'pre-http-header-fullpage');



        //else user is logged in, return unencoded user id
        return $row->user_id;
    }

    function getID(){
        return $this->user_id;
    }

    function getStringID(){
        return $this->user_data->user_string_id;
    }

    function getUsername(){
        return $this->user_data->username;
    }

    function getEmail(){
        return $this->user_data->email_address;
    }

    function isUser(){
        if($this->user_data->is_user == '1')
            return true;

        return false;
    }

    function isEmailValidated(){
        return ($this->user_data->is_email_validated === '1');
    }

    protected $grants = null;
    /**
     * get all user's grants as an array
     */
    function getGrants(){
        if($this->grants === null){

            //if user part of a user group
            if($this->getCustomValue('user_group') != ''){
                $group_grants = db::query('select * from `group_grants` where `group_id` = '
                    .db::quote(user::getUserLoggedIn()->getCustomValue('user_group')));
                while($grant = $group_grants->fetchRow()){
                    $this->grants[$grant->grant_name] = $grant;
                }
            }

            $query = db::query('SELECT * FROM `user_grants` where `user_id` = '.$this->getID());
            while($grant = $query->fetchRow()){
                $this->grants[$grant->grant_name] = $grant;
            }
        }
        if($this->grants === null){
            $this->grants = array();
        }
        return $this->grants;
    }

    function hasGrant($grant_name){
        $grants = $this->getGrants();
        if(isset($grants[$grant_name])){
            if($grants[$grant_name]->onOff === '1'){
                return true;
            }else{
                return false;
            }
        }

        if($this->getCustomValue('user_group') === appUtils::getUserGroupID('Super Admin')){
            return true;
        }

        return false;
    }



    static function getUnusedUserStringId(){
        $tries = 0;
        //while random string exists
        while(true){
            if($tries++ > 30){
                throw new toolboxException("Could not generate a new unused user string:".$user_string_id, 1);
            }

            //generate random user string
            $user_string_id = utils::getRandomString(4);

            //check if random string exists
            if(db::query('select * from `users`
            where `user_string_id` = '.db::quote($user_string_id))->rowCount() === 0){
                return $user_string_id;
            }
        }


    }

    static function ipHasAccount($ip_address = null){
        if($ip_address === null){
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        return ((int)db::query('select count(*) as `count`
        from `users` where `ip` = '.db::quote(inet_pton($ip_address)))->fetchRow()->count > 0);

    }

	private $custom_values = array();
    /**
     * returns null if not exists
     */
    function getCustomValue($field){
    	if(isset($this->custom_values[$field])){
    		return $this->custom_values[$field];
    	}
        $limit = 50;
        if(strlen($field) > $limit){
            throw new toolboxException("Field name must be less than ".$limit." chars! name = ".$field, 1);
        }

        $query = db::query('select * from `user_store` where `user_id` = '.$this->getID().' and `name` = '.db::quote($field));
        if($query->rowCount() === 0){
        	$this->custom_values[$field] = null;
            return null;
        }

    	$this->custom_values[$field] = $query->fetchRow()->value;
        return $this->custom_values[$field];

    }

    function setCustomValue($field, $value){
        db::query('INSERT INTO `user_store` (`user_id`, `name`, `value`, `date_created`)
                    VALUES ('.$this->getID().', '.db::quote($field).', '.db::quote($value).', NOW())
                    ON DUPLICATE KEY UPDATE `value` = '.db::quote($value).', `date_updated` = NOW()');

        return $this;
        return new store();
    }

    function deleteValue($field){
        db::query('delete from `user_store` where `user_id` = '.$this->getID().' AND `name` = '.db::quote($field));
        return $this;
        return new store();
    }



    function sendEmailChange($new_email){

        $this->setCustomValue('email_change', $new_email);

        //get token or throw error if too many
        $token_object = new tokenHelper($this, 'email_change');

        $url = utils::getHost()
                .'/change_email/'.$this->getStringID()
                .'/'.$token_object->getToken();

        //send email
        appUtils::sendEmail($new_email, config::get()->getConfig('app_name').' Email Change Verification Link',
            'Click the link below to verify your email change request for your '
                .config::get()->getConfig('app_name').' account.'
                .'<br> If you did not request this, please ignore this email.'
                .'<br><br><a href="'.$url.'">'.$url.'</a>'
                .'<br><br>PLEASE NOTE: Your login email will be changed to <b>'
                .$new_email.'</b> once you click this link.');

    }


    function getQuickLoginUrl(){

        //get existing token or generate new one
        $token_object = new tokenHelper($this, 'quick_login');

        $url = utils::getHost().'/quick_login/'.$this->getStringID().'/'.$token_object->getToken();

        return $url;
    }


    private $package;
    function getPackage($reset = false){
        if($this->package === null || $reset === true){
            $this->package = new package($this->user_data->package_id);
        }

        return $this->package;
        return new package();//for IDE typehinting
    }

    /**
     * send an email verification link to user.
     * throws userException if user sent too many.
     */
    function sendEmailVerification(){

        //get token or throw error if too many
        $token_object = new tokenHelper($this, 'email_verification');

        $url = utils::getHost().'/verify_email/'.$this->getStringID().'/'.$token_object->getToken();

        //send email
        appUtils::sendEmail(
            $this->getEmail(),
            config::get()->getConfig('app_name').' Email Verification Link',
            'Click the link below to verify your email address associated with your '
            .config::get()->getConfig('app_name').' account.'
            .'<br><br><a href="'.$url.'">'.$url.'</a>');


    }

    function setGrant($grant_name, $on = true){
        if($on){
            db::query('replace into `user_grants` (`user_id`, `grant_name`, `onOff`)
            values ('.db::quote($this->getID()).', '.db::quote($grant_name).', "1")');
        }else{
            db::query('replace into `user_grants` (`user_id`, `grant_name`, `onOff`)
            values ('.db::quote($this->getID()).', '.db::quote($grant_name).', "0")');
        }

        return $this;
        return new user();
    }

    /**
     * send an email verification link to user.
     * throws userException if user sent too many.
     */
    function sendEmail($subject, $body){
        $body = 'Dear '.$this->getUsername().',
                <br><br>'.$body;

        return appUtils::sendEmail(
            $this->getEmail(),
            $subject,
            $body);
    }


    function sendPasswordResetEmail(){

        //get token or throw error if too many
        $token_object = new tokenHelper($this, 'password_reset');

        $url = utils::getHost().'/password_reset/'.$this->getStringID().'/'.$token_object->getToken();

        //send email
        appUtils::sendEmail($this->getEmail(), config::get()->getConfig('app_name').' Password Recovery Request',
            'Click the link below to reset your '.config::get()->getConfig('app_name').' password '
            .'associated with this email account. If you did not request a '
            .'password reset, please disregard this email.'
            .'<br><br><a href="'.$url.'">'.$url.'</a>');


    }

    function getField($field_name){
        if(!property_exists($this->user_data, $field_name)){
            throw new toolboxException('User data does not exist for '.$field_name);
        }

        return $this->user_data->{$field_name};


    }

    function hasValidatedEmail(){
        return ($this->user_data->is_email_validated === '1');
    }

    function setEmailConfirmed($bool = true){
        if($bool){
            db::query('update `users` set `is_email_validated` = 1 where `user_id` = '.$this->getID());
            $this->user_data->is_email_validated = '1';
        }else{
            db::query('update `users` set `is_email_validated` = 0 where `user_id` = '.$this->getID());
            $this->user_data->is_email_validated = '0';
        }
    }

    function setEmailAddress($new_email_address){
        if(!isValid::email($new_email_address)){
            throw new userException("Invalid Email Address: ".$new_email_address, 1);
        }

        db::query('update `users` set `email_address` = '.db::quote($new_email_address)
            .', `is_email_validated` = 0 where `user_id` = '.$this->getID());

    }

    function setPassword($new_password){
        if(!isValid::password($new_password)){
            throw new userException("Invalid password: ".$new_password, 1);
        }

        db::query('update `users` set `password_hash` = '.db::quote(utils::hash($new_password))
            .' where `user_id` = '.$this->getID());

    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($user_id){
        if (!isset(user_framework::$instances['user_id'][$user_id])) {
            return user_framework::$instances['user_id'][$user_id] = new user($user_id);
        }

        return user_framework::$instances['user_id'][$user_id];
        return new user();
    }

    public static function getByStringID($user_string_id){
        if (!isset(user_framework::$instances['user_string_id'][$user_string_id])) {
            return user_framework::$instances['user_string_id'][$user_string_id] = new user($user_string_id, true);
        }

        return user_framework::$instances['user_string_id'][$user_string_id];
        return new user();
    }

    public static function getByEmail($email){
        if (!isset(user_framework::$instances['email'][$email])) {
            return user_framework::$instances['email'][$email] = new user($email, 'email');
        }

        return user_framework::$instances['email'][$email];
        return new user();
    }


}
class userNotLoggedIn extends toolboxException {}
class userNotFound extends toolboxException {}
class userException extends toolboxException {}