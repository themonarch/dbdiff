<?php
namespace toolbox;
class data_stmt{
	public $data = null;
	function __construct($data){
	    if(!is_array($data) && !is_object($data)){
	        throw new toolboxException("Error Processing Request: data variable not array! ".utils::array2string($data), 1);

	    }
		$this->data = $data;
	}

    private $next = false;
	/**
	 * Returns false of no rows
	 */
	function fetchRow(){
	    if($this->next){
            return next($this->data);
	    }
	    $this->next = true;

		return current($this->data);
	}

	/**
	 * Returns null of no rows
	 */
	/*function fetchRowArray($type = MYSQLI_ASSOC){
		return $this->stmt->fetch_array( $type);
	}*/

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
	 * returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement
	 * executed by the corresponding query.
	 * If SQL statement was a SELECT statement, returns the number of rows
	 * returned by that statement.
	 */
	function rowCount(){
		return count($this->data);
	}

	/**
	 * same as rowCount(), but because mysql counts row updates as 2 affected rows, this
	 * will attempt to account for that and get a more accurate count.
	 */
	function rowCountReal(){
		return $this->rowCount();
	}

	function seek($int){
	    $this->next = false;
	    if(empty($this->data)){
	        return $this;
	    }
        if(key($this->data) === null || key($this->data) > $int){
            reset($this->data);
        }
		while (key($this->data) !== $int){
            next($this->data);
		}
		return $this;
		return new array_stmt();
	}
}
