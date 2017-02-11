<?php
//older versions of php < 5.4 don't have $_SERVER["REQUEST_TIME_FLOAT"]
// and not set by default if called via cli
if(!isset($_SERVER["REQUEST_TIME_FLOAT"])){
    $_SERVER["REQUEST_TIME_FLOAT"] = microtime(true);
}

//set timezone here to keep things consistent across environments
date_default_timezone_set('UTC');
//date_default_timezone_set('America/Los_Angeles');

// if executing via cli, we need to fix some things
if (php_sapi_name() === 'cli') {
	global $argv;//the command line arguments

    //first cmd argument is the uri we want to hit
    if(!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == ''){
        $_SERVER['REQUEST_URI'] = "/".ltrim($argv[1], '/');
    }

    if(!isset($_GET) || empty($_GET)){
        //extract any GET parameters from uri string and store
        //in $_GET var (because these aren't automatically set in cli mode)
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $_GET);
        $_REQUEST = $_GET;
    }

}

