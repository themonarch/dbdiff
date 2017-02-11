<?php
namespace toolbox;
class db_stmt{
	public $stmt = null;
	private $last_insert_id = null;
	private $affected_rows = null;
	function __construct($stmt, $last_insert_id, $affected_rows){
		$this->stmt = $stmt;
		$this->last_insert_id = $last_insert_id;
		$this->affected_rows = $affected_rows;
	}

	/**
	 * Returns null of no rows
	 */
	function fetchRow(){
		return $this->stmt->fetch_object();
	}

	/**
	 * Returns null of no rows
	 */
	function fetchRowArray($type = MYSQLI_ASSOC){
		return $this->stmt->fetch_array( $type);
	}

	/**
	 * Returns null of no rows
      NOT RECOMMENDED + REQUIRES NATIVE DRIVER
	function fetchAllRows(){
		return $this->stmt->fetch_all(MYSQLI_ASSOC);
	}   */

	/**
	 * get NEXT result row but return only the value of a column specified by 0-based index

	function fetchColumn($colnum = null){
		return $this->stmt->fetchColumn($colnum);
	}
	 */

 	/**
	 * returns (int) 0 if not inserted
	 */
	function last_insert_id(){
		return $this->last_insert_id;
	}

	/**
	 * returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement
	 * executed by the corresponding query.
	 * If SQL statement was a SELECT statement, returns the number of rows
	 * returned by that statement.
	 */
	function rowCount(){
		if(!is_object($this->stmt) || $this->stmt->num_rows === null){
			return $this->affected_rows;
		}
		return $this->stmt->num_rows;
	}

	/**
	 * same as rowCount(), but because mysql counts row updates as 2 affected rows, this
	 * will attempt to account for that and get a more accurate count.
	 */
	function rowCountReal(){
		$num_rows = $this->rowCount();
		if($num_rows === 2){
			$num_rows = 1;
		}
		return $num_rows;
	}

	function seek($int){
		$this->stmt->data_seek($int);
		return $this;
		return new db_stmt();
	}

	function freeResult(){
		$this->stmt->free_result();
		return $this;
		return new db_stmt();
	}

   /*function __destruct() {
       @$this->freeResult();
   }*/

}
