<?php namespace toolbox;
class connection{

	private $connection_data;
    function __construct($connection_id){
    	$query = db::query('select * from `db_connections`
    		where `connection_id` = '.db::quote($connection_id).'
    		and `user_id` = '.db::quote(user::getUserLoggedIn()->getID()));

		if($query->rowCount() == 0){
			throw new toolboxException("Connection not found: ".$connection_id, 1);
		}

		$this->connection_data = $query->fetchRow();


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
			return $this->getUser().'@'.$this->getHost();
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
		return db::query($sql, $this->getID());
		return new db_stmt();
	}

	function connect(){
		if(db::isConnected($this->getID())){
			return $this;
		}
		try{//try connection
			db::connect(
				$this->getHost(),
				$this->getUser(),
				$this->getPass(),
				null,
				$this->getID(),
				$this->getPort());
			db::setDB();
		}catch(toolboxException $e){
			throw new connectionException($e->getMessage(), 1);
		}

		return $this;
		return new sync();
	}
    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($name = 'singleton'){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
        return new self();
        return new connection();
    }


}
class connectionException extends toolboxException{};
