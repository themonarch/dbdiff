<?php namespace toolbox;
class connection{

	private $connection_data;
	private $db;
	private $prefix;
    function __construct($connection_id, $db, $prefix){
    	$query = db::query('select * from `db_connections`
    		where `connection_id` = '.db::quote($connection_id).'
    		and `user_id` = '.db::quote(user::getUserLoggedIn()->getID()));

		if($query->rowCount() == 0){
			throw new toolboxException("Connection not found: ".$connection_id, 1);
		}

		$this->connection_data = $query->fetchRow();
		$this->db = $db;
		$this->prefix = $prefix;


    }

	function getID(){
		return $this->getData('connection_id');
	}

	function getData($key){
		if(!isset($this->connection_data->{$key})){
			throw new toolboxException("Error: $key not set!", 1);
		}

		return $this->connection_data->{$key};
	}

	function getHost(){
		return $this->getData('host');
	}


	function getPort(){
        return $this->getData('port');
	}

	function getUser(){
		return $this->getData('user');
	}

	function getPass(){
		return user::get($this->getData('user_id'))->decrypt($this->getData('password'));
	}

	function getName(){
		$name = '';
		try{
			$name = $this->getData('name');
		}catch(toolboxException $e){
		}

		if($name == ''){
			return $this->getHost();
		}

		return $name;
	}

	private $tables;
	function getNextDB(){
		if($this->tables == null){
			$this->tables = db::query('SHOW DATABASES', $this->getID());
		}
		$row = $this->tables->fetchRow();
		if($row == null){
			$this->tables->seek(0);
			return null;
		}
		return $row->Database;
	}

	function belongsTo($user_id){
		if((int)$this->getData('user_id') === (int)$user_id){
			return true;
		}
		return false;
	}

	function isConnected(){
		return db::isConnected($this->getID());
	}

	function query($sql){
		return db::query($sql, $this->getDBID());
		return new db_stmt();
	}

    function getDBID(){
        return $this->getID().'-'.$this->db.'-'.$this->prefix;
    }

	function connect(){
	    $db_id = $this->getDBID();
		if(db::isConnected($db_id)){
			//return $this;
			throw new toolboxException('Already connected to DB with id of: '.$db_id);

		}
		try{//try connection
			db::connect(
				$this->getHost(),
				$this->getUser(),
				$this->getPass(),
				$this->db,
				$db_id,
				$this->getPort());
			db::setDB();
		}catch(toolboxException $e){
			throw new connectionException('Error while trying to connect to '
				.utils::htmlEncode($this->getHost()).' with error message: <b>'
				.utils::htmlEncode($e->getMessage()).'</b>');
		}

		return $this;
		return new sync();
	}
    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($name = 'singleton', $db = '', $prefix = ''){
        if (
            !isset(self::$instances[$name])
            || !isset(self::$instances[$name][$db])
            || !isset(self::$instances[$name][$db][$prefix])
        ){
            self::$instances[$name][$db][$prefix] = new self($name, $db, $prefix);
        }
        return self::$instances[$name][$db][$prefix];
        return new self();
        return new connection();
    }


}
class connectionException extends toolboxException{};
