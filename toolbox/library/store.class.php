<?php
namespace toolbox;
/**
 * Template helper for rendering ALL output to the user (templates/views/etc.).
 */
class store {
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

	function createTableIfNotExists(){
		//if table NOT exists
		db::query("CREATE TABLE `store` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(250) NOT NULL,
				`value` VARCHAR(2000) NOT NULL,
				`date_created` DATETIME NOT NULL,
				`date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `name` (`name`)
			)
			COMMENT='generic storage'
			COLLATE='utf8_general_ci'
			ENGINE=MEMORY
			AUTO_INCREMENT=1;
		");

		return $this;
		return new store();
	}

	/**
	 * Returns null if value not set.
	 */
	function getValue($field){
        $limit = 250;
        if(strlen($field) > $limit){
            throw new toolboxException("Field name must be less than ".$limit." chars! name = ".$field, 1);
        }

		if($this->driver == 'db'){
			$query = db::query('select * from `store` where `name` = '.db::quote($field));
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

	function setValue($field, $value){
		if($this->driver == 'db'){
		    if($value === null){
		        $value = '';
		    }
			db::query('INSERT INTO `store` (`name`, `value`, `date_created`)
						VALUES ('.db::quote($field).', '.db::quote($value).', NOW())
						ON DUPLICATE KEY UPDATE `value` = '.db::quote($value).'');
		}elseif($this->driver == 'apc'){
			apc_store($field, $value);
		}elseif($this->driver == 'xcache'){
			xcache_set($field, serialize($value));
		}elseif($this->driver == 'blackhole'){
			//do nothing
		}

		return $this;
		return new store();
	}

	function increaseCounter($field){
		if($this->driver == 'db'){
			db::query('INSERT INTO `store` (`name`, `value`, `date_created`)
				VALUES ('.db::quote($field).', 1, NOW())
				ON DUPLICATE KEY UPDATE `value` = `value`+1');
            return $this->getValue($field);
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

	function decreaseCounter($field){
		if($this->driver == 'db'){
			db::query('INSERT INTO `store` (`name`, `value`, `date_created`)
				VALUES ('.db::quote($field).', 1, NOW())
				ON DUPLICATE KEY UPDATE `value` = `value`-1');
            return $this->getValue($field);
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

	function deleteValue($field){
		if($this->driver == 'db'){
			db::query('delete from `store` where `name` = '.db::quote($field));
		}elseif($this->driver == 'apc'){
			apc_delete($field);
		}elseif($this->driver == 'xcache'){
			xcache_unset($field);
		}elseif($this->driver == 'blackhole'){
			//do nothing
		}

		return $this;
		return new store();
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
		return new store();
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
		return new store();
	}


	public static function instanceExists($name){
		if(isset(self::$instances[$name])){
			return true;
		}

		return false;
	}


}