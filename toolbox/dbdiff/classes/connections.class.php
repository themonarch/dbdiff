<?php namespace toolbox;
class connections{

	private $query;
    function __construct(){
    	$this->query = db::query('select * from `db_connections`');

    }

    function connect($server = null){
    	if($server === null){
    		$server = $this->getPropertyName();
    	}
        $db_info = $this->all_servers[$server];
        try{
            db::connect($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['db'], $server);
        }catch(toolboxException $e){
            if(utils::stringStartsWith($e->getMessage(), 'Failed to connect to MySQL: (2002) php_network_getaddresses: getaddrinfo failed:')){
                //try again
                db::connect($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['db'], $server);
            }else{
                throw $e;
            }
        }

        db::setDB();

        return $this;
        return new connections();
    }


	function getNextConnection(){
		if(!($row = $this->query->fetchRow())){
			$this->query->seek(0);
			return null;
		}

		return connection::get($row->connection_id);
		return connection();

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
        return new connections();
    }


}
