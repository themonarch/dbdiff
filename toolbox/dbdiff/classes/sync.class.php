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

	function excludeTable($table_name){
		$tables_excluded = $this->getExcludedTables();

		$tables_excluded[] = $table_name;

		db::query('update `db_sync_profiles`
			set `tables_excluded` = '.db::quote(json_encode(array_unique($tables_excluded))).'
			where `id` = '.db::quote($this->getID()));


	}

	function includeTable($table_name){
		$tables_excluded = $this->getExcludedTables();
		if(($key = array_search($table_name, $tables_excluded)) !== false) {
		    unset($tables_excluded[$key]);
		}

		db::query('update `db_sync_profiles`
			set `tables_excluded` = '.db::quote(json_encode(array_unique($tables_excluded))).'
			where `id` = '.db::quote($this->getID()));


	}

	function updateLastViewed(){
		db::query('update `db_sync_profiles` set `last_viewed` = now() where `id` = '.db::quote($this->getID()));
		return $this;
		return new sync();
	}

	function getData($key){
		return $this->data->{$key};
	}

    function connectSource(){
        $this->getSourceConnection()->connect();
    }

    function connectTarget(){
        $this->getTargetConnection()->connect();

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
		return connection::get($this->getData('source_conn_id'), $this->getSourceDB(), '-source');
		return new connection();
	}

	function getExcludedTables($escape = false){
		$tables = (array)json_decode($this->getData('tables_excluded', true));

		if($escape){
			array_walk($tables, function(&$item1, $key){
			    $item1 = db::quote($item1);
			});
		}

		return $tables;
	}

	function getSourceCreate($table_name){
		return $this->getCreate($this->getSourceConnection(), $this->getSourceDB(), $table_name);
	}

	function getTargetCreate($table_name){
		return $this->getCreate($this->getTargetConnection(), $this->getTargetDB(), $table_name);
	}

	private function getCreate($conn, $db, $table){
		try{
			$schema = $conn->query('SHOW CREATE TABLE `'
			.$db.'`.`'.appUtils::escapeField($table).'`')->fetchRow()->{'Create Table'};

			//fix for old versions of mysql that have double spaces
			$schema = str_replace('  ', ' ', $schema);

			//fix for old versions of mysql that lowercase some field properties
			$schema = explode("\n", $schema);
			foreach ($schema as &$line) {
				$tick_position = strrpos ($line, '`');
				if($tick_position !== false){
					$before_tick = substr($line, 0, $tick_position);
					$line = $before_tick.strtoupper(substr($line, $tick_position));
				}
			}

			$schema = implode("\n", $schema);

			return $schema;
		}catch(toolboxException $e){
			if(utils::stringStartsWith($e->getMessage(),
				'Table \''.$db.'.'.$table.'\' doesn\'t exist')){
				return '';
			}
			throw $e;
		}
	}

	function updateTargetConnection($connection_id, $db){
		db::query('update `db_sync_profiles`
			set `target_db` = '.db::quote($db).',
			`target_conn_id` = '.db::quote($connection_id).'
			where `id` = '.db::quote($this->getID())
		);
	}

	function updateSourceConnection($connection_id, $db){
		db::query('update `db_sync_profiles`
			set `source_db` = '.db::quote($db).',
			`source_conn_id` = '.db::quote($connection_id).'
			where `id` = '.db::quote($this->getID())
		);
	}


	function getSourceDB(){
		return $this->getData('source_db');
	}

	function getTargetDB(){
		return $this->getData('target_db');
	}

	function getTargetConnection(){
		return connection::get($this->getData('target_conn_id'), $this->getTargetDB(), '-target');
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
