<?php
namespace toolbox;
/**
 * simple timer logic
 */
class stopWatch {

	function __construct(){
		$this->startTimer();
	}

	private $start_time;
	/**
	 * Start or reset the timer to start from now.
	 */
	function startTimer(){
			$this->start_time = microtime(true);
	}

	/**
	 * Get number of seconds since startTimer()
	 */
	function getElapsedTime(){
		return round((microtime(true) - $this->start_time), 4);
	}

	/**
	 * Get number of seconds since start of php
	 */
	function getSessionTime(){
		if(!isset($_SERVER["REQUEST_TIME_FLOAT"])){
    		throw new stopWatchException("REQUEST_TIME_FLOAT is not set on this server!", 1);
		}
		return round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]), 4);
	}

	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create(){
		return new self();
		return new stopWatch();
	}

	/**
	 * Get THE singleton of class instance
	 * (creates it if not exists)
	 */
	private static $instance;
	public static function get(){
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
		return new self();
        return new stopWatch();
	}
}

class stopWatchException extends toolboxException {}