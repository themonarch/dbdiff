<?php namespace toolbox;
class bench {
    private static $results = array();
    private static $timer;

	private static $running = true;
	static function pause(){
		self::$running = false;
	}
	static function resume(){
		self::$running = true;
	}

	static function isRunning(){
		return self::$running;
	}

	/**
	 * Start or finish timing an event
	 */
    static function mark($event_name){
    	if(!self::$running){
    		return;
    	}

		if(self::$timer === null){
			self::$timer = benchmark::create();
		}

		//if event previously started
		if(isset(self::$results[$event_name])){
			//if currently running
			if(isset(self::$results[$event_name]['start'])){
				//finish it
				$time = microtime(true) - self::$results[$event_name]['start'];
				unset(self::$results[$event_name]['start']);

				//if finished for the first time
				if(self::$results[$event_name]['count'] == 0){
					self::$results[$event_name]['count']++;
					self::$results[$event_name]['avg'] = $time;
					self::$results[$event_name]['total'] = $time;
					self::$results[$event_name]['max'] = $time;
				}else{//repeated event
					self::$results[$event_name]['count']++;
					self::$results[$event_name]['avg'] = (
						self::$results[$event_name]['avg'] * self::$results[$event_name]['count'] + $time
						)/(self::$results[$event_name]['count']+1);;
					self::$results[$event_name]['total'] += $time;
					if(self::$results[$event_name]['max'] < $time){
						self::$results[$event_name]['max'] = $time;
					}
				}
			}else{
				//start it
				self::$results[$event_name]['start'] = microtime(true);

			}

		}else{//event starting for first time
			self::$results[$event_name] = array(
				'start' => microtime(true),
				'count' => 0
			);
		}

    }

	static function getResults(){
		return self::$results;
	}


}
