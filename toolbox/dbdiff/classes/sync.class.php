<?php namespace toolbox;
class sync{

	private $data;
    function __construct($sync_id){
    	$query = db::query('select * from `db_sync_profiles`
    		where `id` = '.db::quote($sync_id));

		if($query->rowCount() == 0){
			throw new toolboxException("Sync not found: ".$sync_id, 1);
		}

		$this->data = $query->fetchRow();

    }

	function getID(){
		return $this->getData('id');
	}

	function getData($key){
		if(!isset($this->data->{$key})){
			throw new toolboxException("Error: $key not set!", 1);
		}

		return $this->data->{$key};
	}

	function getName(){
		$name = trim($this->getData('description'));
		if($name == ''){
			$name = $this->getSourceConnection()->getName() .' vs. '.$this->getTargetConnection()->getName();
		}
		return $name;
	}

	function belongsTo($user_id){
		if((int)$user_id === (int)$this->getData('user_id')){
			return true;
		}

		return false;
	}

	function getSourceConnection(){
		return connection::get($this->getData('source_conn_id'));
		return new connection();
	}

	function getSourceCreate($table_name){
		return $this->getCreate($this->getSourceConnection(), $this->getSourceDB(), $table_name);
	}

	function getTargetCreate($table_name){
		return $this->getCreate($this->getTargetConnection(), $this->getTargetDB(), $table_name);
	}

	private function getCreate($conn, $db, $table){
		try{
		return $conn->query('SHOW CREATE TABLE `'
			.$db.'`.`'.$table.'`')->fetchRow()->{'Create Table'};
		}catch(toolboxException $e){
			if(utils::stringStartsWith($e->getMessage(),
				'Table \''.$db.'.'.$table.'\' doesn\'t exist')){
				return '';
			}
			throw $e;
		}
	}


	function getSourceDB(){
		return $this->getData('source_db');
	}

	function getTargetDB(){
		return $this->getData('target_db');
	}

	function getTargetConnection(){
		return connection::get($this->getData('target_conn_id'));
		return new sync();
	}

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($name){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self($name);
        }
        return self::$instances[$name];
        return new self();
        return new sync();
    }


}
