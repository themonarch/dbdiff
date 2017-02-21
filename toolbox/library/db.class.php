<?php
namespace toolbox;
class db {

    private static $mysqli = array();
    private static $db_id = 'default';

	/**
	 * Set the current connection to use.
	 */
    public static function setDB($id = 'default'){
        if(!isset(self::$mysqli[$id])){
            throw new dbException("DB id not found! db = " .$id);
        }

        self::$db_id = $id;
    }

    public static function getDB(){
        return self::$db_id;
    }

    public static function connect($host, $user, $pass, $database = '', $id = 'default', $port = null, $timeout = 3){
        if(isset(self::$mysqli[$id])){
            throw new dbException("Tried to connect to db with id that is already being used! id = ".$id);
        }

        self::$mysqli[$id] = mysqli_init( );
        if(!self::$mysqli[$id]->options( MYSQLI_OPT_CONNECT_TIMEOUT, $timeout))
            throw new dbException("Couldn't set mysqli option: ".self::$mysqli[$id]->error);
        try{
            @self::$mysqli[$id]->real_connect($host, $user, $pass, $database, $port);
        }catch(toolboxError $e){
            throw new dbException($e->getMessage());
        }
        if (self::$mysqli[$id]->connect_errno) {
            $connect_errno = self::$mysqli[$id]->connect_errno;
            $connect_error = self::$mysqli[$id]->connect_error;
            unset(self::$mysqli[$id]);
            throw new dbException("Failed to connect to MySQL: (" . $connect_errno . ") "
                    . $connect_error, $connect_errno);
        }
        self::$mysqli[$id]->set_charset("utf8");
        self::setDB($id);
    }


    public static function duplicateDB($id, $duplicate_id = null){
        if($duplicate_id === null){
            $duplicate_id = $id;
            $id = db::getDB();
        }
        self::$mysqli[$duplicate_id] = self::getMysqli($id);
    }

    public static function disconnect($id = 'default'){
        if(!self::isConnected($id)){
            return;
        }
        $mysqli = self::getMysqli($id);
        $mysqli->close();
        unset(self::$mysqli[$id]);
    }

    public static function insert($table, $field_value, $update = array(), $db_id = null){
        $stmt = null;
        $sql = 'INSERT INTO `'.$table.'`(';

        if(count($field_value) > 0){
            foreach($field_value as $key => $value){
                $sql .= '`'.$key.'`, ';
            }
            $sql = rtrim($sql, ', ');
        }

        $sql .= ') VALUES (';

        if(count($field_value) > 0){
            foreach($field_value as $key => $value){
                $sql .= $value.', ';
            }
            $sql = rtrim($sql, ', ');
        }

        $sql .= ')';


        if(is_array($update) && !empty($update)){
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            foreach($update as $key => $value){
                $sql .= '`'.$key.'` = VALUES(`'.$key.'`), ';
                unset($update[$key]);
                //$update[':'.$key] = $value;
            }
            $sql = utils::removeStringFromEnd($sql,  ', ');
        }

        return self::query($sql, $db_id);

    }

    public static function replace($table, $key_value_pairs, $db_id = null){
        $stmt = null;
        $sql = 'REPLACE INTO `'.$table.'`(';

        if(count($key_value_pairs) > 0){
            foreach($key_value_pairs as $key => $value){
                $sql .= '`'.$key.'`, ';
            }
            $sql = rtrim($sql, ', ');
        }

        $sql .= ') VALUES (';

        if(count($key_value_pairs) > 0){
            foreach($key_value_pairs as $key => $value){
                $sql .= db::quote($value).', ';
            }
            $sql = rtrim($sql, ', ');
        }

        $sql .= ')';

        return self::query($sql, $db_id);

    }

    private static function getMysqli($db_id = null){

        if($db_id !== null){
            if(self::$mysqli[$db_id] === null){
                throw new dbException('db not connected!');
            }else{
                return self::$mysqli[$db_id];
            }
        }

        if(!isset(self::$mysqli[self::$db_id]) || self::$mysqli[self::$db_id] === null){
            throw new dbException('db not connected!');
        }

        return self::$mysqli[self::$db_id];

    }

    static function isConnected($db_id = null){

        if($db_id !== null){
            if(!isset(self::$mysqli[$db_id]) || self::$mysqli[$db_id] === null){
                return false;
            }else{
                return true;
            }
        }

        if(!isset(self::$mysqli[self::$db_id]) || self::$mysqli[self::$db_id] === null){
            return false;
        }

        return true;

    }


    private static function execute($sql, $db_id = null, $result_mode = null){
        if($db_id !== null){
            $old_db_id = db::getDB();
            db::setDB($db_id);
        }else{
            $old_db_id = null;
        }

        if(bench::isRunning()){
            $bench_action = substr($sql, 0, 4000);
            bench::mark($bench_action);
            $stmt = db::getMysqli()->query($sql, $result_mode);
            bench::mark($bench_action);
        }else{
            $stmt = db::getMysqli()->query($sql, $result_mode);
        }

        if(db::getMysqli()->error != ''){
            $error = db::getMysqli()->error;
            if($old_db_id !== null){
                db::setDB($old_db_id);
            }
            throw new dbException($error . ' ['.$sql.']');
        }

        if($old_db_id !== null){
            db::setDB($old_db_id);
        }

        return $stmt;
    }

    public static function query($sql, $db_id = null, $result_mode = null){

        return new db_stmt(
	        db::execute($sql, $db_id, $result_mode),
	        db::getMysqli($db_id)->insert_id,
	        db::getMysqli($db_id)->affected_rows
		);

    }


    public static function asyncQuery($sql, $db_id = null){
        db::execute($sql, $db_id, MYSQLI_ASYNC);
		$stmt = new db_async_stmt(db::getMysqli($db_id));
		return $stmt;
        return new db_async_stmt();
    }

    public static function quote($string)
    {
        if($string === null){
            return 'NULL';
        }

        return '"'.db::getMysqli()->real_escape_string($string).'"';
    }

    static function likeEscape($s, $e = '\\'){
        return db::getMysqli()->real_escape_string(str_replace(array($e, '_', '%'), array($e.$e, $e.'_', $e.'%'), $s));
    }

    public static function escape($string)
    {
        if($string === null){
            return 'NULL';
        }

        return db::getMysqli()->real_escape_string($string);
    }


}

class dbException extends toolboxException {}