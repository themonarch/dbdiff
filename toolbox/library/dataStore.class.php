<?php
namespace toolbox;
/**
 * Template helper for rendering ALL output to the user (templates/views/etc.).
 */
class dataStore {
	private $driver;
	function __construct($driver = null){
		if($driver === null){
			try{
				$driver = config::get()->getConfig('cache_driver');
			}catch(toolboxException $e){
				$driver = null;
			}
		}
		if($driver == 'db'){
			$this->driver = 'db';
		}elseif($driver == 'apc'){
			$this->driver = 'apc';
		}elseif($driver == 'memcache'){
			$this->driver = 'memcache';
		}elseif($driver == 'xcache'){
			$this->driver = 'xcache';
		}elseif($driver == 'blackhole'){
			$this->driver = 'blackhole';
		}else{
			throw new toolboxException('Unsupported cache driver: '.$driver, 1);
		}

	}


	/**
	 * Returns null if value not set.
	 */
	function getValue($field, $type = 'none', $type_id = 0){
        $limit = 250;
        if(strlen($field) > $limit){
            throw new toolboxException("Field name must be less than ".$limit." chars! name = ".$field, 1);
        }

		if($this->driver == 'db'){
			$query = db::query('select * from `data_store`
			where `type` = '.db::quote($type).'
			and `type_id` = '.db::quote($type_id).'
			and `name` = '.db::quote($field));
			if($query->rowCount() === 0){
				return null;
			}

			return $query->fetchRow()->value;
		}elseif($this->driver == 'apc'){
			$value = apc_fetch($field);
			if($value === false){
				return null;
			}

			return $value;
		}elseif($this->driver == 'xcache'){
			$value = xcache_get($field);
			if(gettype($value) === 'string'){
				return unserialize($value);
			}
			return $value;
		}elseif($this->driver == 'blackhole'){
			return null;
		}

	}

	function setValue($field, $value, $type = 'none', $type_id = 0){
		if($this->driver == 'db'){
		    if($value === null){
		        $value = '';
		    }
			db::query('INSERT INTO `data_store` (`type`, `type_id`, `name`, `value`, `date_created`)
						VALUES ('.db::quote($type).', '.db::quote($type_id).', '.db::quote($field).', '.db::quote($value).', NOW())
						ON DUPLICATE KEY UPDATE `value` = '.db::quote($value).'');
		}elseif($this->driver == 'apc'){
			apc_store($field, $value);
		}elseif($this->driver == 'xcache'){
			xcache_set($field, serialize($value));
		}elseif($this->driver == 'blackhole'){
			//do nothing
		}

		return $this;
		return new dataStore();
	}

	function increaseCounter($field, $type = 'none', $type_id = 0){
		if($this->driver == 'db'){
			db::query('INSERT INTO `data_store` (`type`, `type_id`, `name`, `value`, `date_created`)
				VALUES ('.db::quote($type).', '.db::quote($type_id).', '.db::quote($field).', 1, NOW())
				ON DUPLICATE KEY UPDATE `value` = `value`+1');
            return $this->getValue($field, $type, $type_id);
		}elseif($this->driver == 'apc'){
			if(!($count = apc_inc($field))){
				$this->setValue($field, 1, true);
                $count = 1;
			}
            return $count;
		}elseif($this->driver == 'xcache'){
			return xcache_inc($field);
		}elseif($this->driver == 'blackhole'){
			//do nothing
		}

	}

	function decreaseCounter($field, $type = 'none', $type_id = 0){
		if($this->driver == 'db'){
			db::query('INSERT INTO `data_store` (`type`, `type_id`, `name`, `value`, `date_created`)
				VALUES ('.db::quote($type).', '.db::quote($type_id).', '.db::quote($field).', 1, NOW())
				ON DUPLICATE KEY UPDATE `value` = `value`-1');
            return $this->getValue($field, $type, $type_id);
		}elseif($this->driver == 'apc'){
			if(!($count = apc_dec($field))){
				$this->setValue($field, 0, true);
                $count = 0;
			}
            return $count;
		}elseif($this->driver == 'xcache'){
			return xcache_dec($field);
		}elseif($this->driver == 'blackhole'){
			//do nothing
		}

	}

	function getCounter($name, $reset_interval, $type = 'none', $type_id = 0){
		if($this->driver == 'db'){
            $row = db::query('select * from `data_store`
			where `type` = '.db::quote($type).'
			and `type_id` = '.db::quote($type_id).'
			and `name` = '.db::quote($name).'
            and `date_created` > '.db::quote(date('Y-m-d H:i:s', strtotime('-'.$reset_interval))))->fetchRow();
            if($row === null){
                $this->deleteValue($name, $type, $type_id);
                return 0;
            }

            return (int)$row->value;

		}

        throw new toolboxException('Can\'t do a counter for '.$this->driver);

	}

	function deleteValue($field, $type = 'none', $type_id = 0){
		if($this->driver == 'db'){
			db::query('delete from `data_store`
			where `type` = '.db::quote($type).'
			and `type_id` = '.db::quote($type_id).'
			and `name` = '.db::quote($field));
		}elseif($this->driver == 'apc'){
			apc_delete($field);
		}elseif($this->driver == 'xcache'){
			xcache_unset($field);
		}elseif($this->driver == 'blackhole'){
			//do nothing
		}

		return $this;
		return new dataStore();
	}

    function getCacheDriver(){
        return $this->driver;
    }


	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create($driver = null){
		return new self($driver);
		return new dataStore();
	}


	/**
	 * Get THE singleton of class instance
	 * (creates it if not exists)
	 */
	private static $instances;
	public static function get($name = null, $driver = null){
		if($name === null){
			$name = 'db';
		}

        if($driver === null){
            $driver = $name;
        }

		if(!self::instanceExists($name)){
			self::$instances[$name] = new self($driver);
		}

		return self::$instances[$name];
		return new dataStore();
	}


	public static function instanceExists($name){
		if(isset(self::$instances[$name])){
			return true;
		}

		return false;
	}


}