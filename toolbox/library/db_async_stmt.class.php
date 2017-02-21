<?php
namespace toolbox;
class db_async_stmt{
	public $stmt = null;
	private $reaped = false;
	function __construct($stmt){
		$this->stmt = $stmt;
	}


    function isReady(){
        if($this->reaped) return true;
        $links = array($this->stmt);
        return mysqli_poll($links, $links, $links, 0);
    }


	/**
	 *
	 */
	private function reapRow(){
        if($this->reaped) return false;
        $this->reaped = true;
	    $result = $this->stmt->reap_async_query();
		if($result === false){
			throw new dbException($this->stmt->error);
		}
		return new db_stmt($result, $this->stmt->insert_id, $this->stmt->affected_rows);
	}



    function freeUpQuery(){
        return $this->reapRow();
    }

    function fetchRow(){
        if($this->reaped) return false;
        return $this->reapRow()->fetchRow();
    }

}
