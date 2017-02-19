<?php
namespace toolbox;
/**
 * All purpose functions.
 */
class utils {
    /**
     * Extract different parts of a url, including domain, subdomain, etc.
     */
    public static function parseUrl($url) {
        $r  = "^(?:(?P<scheme>\w+)://)?";
        $r .= "(?:(?P<login>\w+):(?P<pass>\w+)@)?";
        $r .= "(?P<host>(?:(?P<subdomain>[-\w\.]+)\.)?" . "(?P<domain>[-\w]+\.(?P<extension>\w+)))";
        $r .= "(?::(?P<port>\d+))?";
        $r .= "(?P<path>[\w~/-]*/(?P<file>[\w-]+(?:\.\w+)?)?)?";
        $r .= "(?:\?(?P<arg>[\w=&]+))?";
        $r .= "(?:#(?P<anchor>\w+))?";
        $r = "!$r!";
        preg_match ( $r, $url, $out );

        if(empty($out)){
            throw new toolboxException("Invalid url passed to utils::parseUrl: $url", 1);
        }elseif(is_numeric(str_replace('.', '', $out['host']))){
            throw new toolboxException("Invalid url passed to utils::parseUrl: $url", 1);
        }

        return $out;
    }

    static function ifElse($value, $equals_to, $else){
        if($value === $equals_to){
            return $else;
        }

        return $value;
    }

    /**
     * Count the number of instances of uri currently running
     */
    public static function countRunningInstances($process_name){
        $output = array();
        $command = 'ps auxww| grep '.$process_name;

        exec($command, $output);
        $running = 0;

        foreach($output as $key => $value){
            if(strstr($value, "/usr/bin/php") !== FALSE){
                $running++;
            }
        }

        return $running;
    }

    /**
     * converts a string to a url safe slug
     */
    public static function toSlug($str) {
        $str = strtolower(trim($str));
        $str = str_replace('-', '_', $str);
        $str = preg_replace('/[^a-z0-9-]/', '_', $str);
        $str = preg_replace('/-+/', "_", $str);
        return $str;
    }

    /**
     * Generates a random string of given length using given characters
     *
     * @param string $valid_chars which characters to use
     * @param string $length length of generated string
     *
     */
    public static function getRandomString(
        $length = 4,
        $valid_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    ){

        // start with an empty random string
        $random_string = "";

        // count the number of chars in the valid chars string so we know how many choices we have
        $num_valid_chars = strlen($valid_chars);

        // repeat the steps until we've created a string of the right length
        for ($i = 0; $i < $length; $i++) {

            // pick a random number from 1 up to the number of valid chars
            $random_pick = mt_rand(1, $num_valid_chars);

            // take the random character out of the string of valid chars
            // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
            $random_char = $valid_chars[$random_pick-1];

            // add the randomly-chosen char onto the end of our string so far
            $random_string .= $random_char;
        }

        // return our finished random string
        return $random_string;
    }

     static function array2string($array) {
        //return print_r($array, TRUE);
        if(is_string($array)){
            return $array;
        }
        return var_export($array, TRUE);
        if(is_string($array) || is_numeric($array)) return $array;
        if(!is_array($array)) return 'utils:::array2string: given param is not an array! ['.gettype($array).']';

        return implode(', '."\t", array_map(function ($v, $k) {
            return "\n".$k . ' = ' . utils::array2string($v); },
                                                                                $array,
                                                                                array_keys($array)));
    }

    static function countDecimals($num) {
        $dec_pos = strpos($num, '.');
        if($dec_pos === false){
            return 0;
        }

        return (strlen($num) - $dec_pos - 1);

    }

    /**
     * Redirect to https if https not already on, preserving all query parameters.
     */
    static function redirectToHttps($http_status_code = 301){
        if(php_sapi_name() === 'cli'){
            return true;
        }

        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
            return true;
        }
        if(isset($_SERVER['HTTP_HOST'])){
            $host = $_SERVER['HTTP_HOST'];
        }else{
            $host = $_SERVER['SERVER_NAME'];
        }
        utils::redirectTo('https://'.$host.$_SERVER['REQUEST_URI'], $http_status_code);
    }

    /**
     * Redirect to https if https not already on, preserving all query parameters.
     */
    static function redirectToNonHttps($http_status_code = 301){
        if(php_sapi_name() === 'cli'){
            return true;
        }

        if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on'){
            return true;
        }
        if(isset($_SERVER['HTTP_HOST'])){
            $host = $_SERVER['HTTP_HOST'];
        }else{
            $host = $_SERVER['SERVER_NAME'];
        }
        utils::redirectTo('http://'.$host.$_SERVER['REQUEST_URI'], $http_status_code);
    }


    /**
     * Redirect to https if https not already on, preserving all query parameters.
     */
    static function redirectTo($location = '/', $http_status_code = 302){

        if(self::isAjax()){
            header("X-Ajax-Redirect: $location");
        }else{
            header("Location: $location", true, $http_status_code);
        }
        die();
    }


    static function isAjax(){
        $headers = utils::getAllHeaders();
        if(isset($headers['X-Ajax']) && $headers['X-Ajax'] === 'true'){
            return true;
        }

        return false;
    }

    /**
     * return md5 hash of a string
     */
    public static function hash($string){
        return md5('#$V'.$string.'^%I*H&5');
    }

    static function enableErrors($strict = false){
        ini_set('display_errors',1);
        ini_set('display_startup_errors',1);
        error_reporting(-1);
    }


    /**
     * A quick print_r + die wrapped in a <pre> tag.
     */
    static function prd($value){
        echo '<pre>';
        print_r($value);
        echo '</pre>';
        exit;
    }

    /**
     * A quick var_dump + die wrapped in a <pre> tag.
     */
    static function vdd($value){
        self::vd($value);
        exit;
    }

    /**
     * A quick var_dump in a <pre> tag.
     */
    static function vd($value){
        echo '<script></script><pre>';
        var_dump($value);
        echo '</pre>';
    }

    /**
     * Get or Set the HTTP response code.
     * If you pass no parameters then http_response_code will get the current status code.
     * If you pass a parameter it will set the response code.
     */
    static function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }


    /**
     * Remove a string from the beginning of another string.
     */
    public static function removeStringFromBeginning($original_string, $remove_string){
        return preg_replace('/^' . preg_quote($remove_string, '/') . '/', '', $original_string);
    }

    /**
     * Remove a string from the end of another string.
     */
    public static function removeStringFromEnd($original_string, $remove_string){
        return preg_replace('/' . preg_quote($remove_string, '/') . '$/', '', $original_string);
    }

    /**
     * Combine and minify (if applicable) files if updated,
     * @return string link to combined file.
     */
    static function combined_css_include($array, $path = null){
        return self::minifyAndCombine($array, 'css', $path);

    }

    /**
     * Combine and minify (if applicable) files if updated,
     * @return string link to combined file.
     */
    static function combined_js_include($array, $path = null){
        return self::minifyAndCombine($array, 'js', $path);

    }

    private static function minifyAndCombine($array, $type, $path = null){
        if($path === null){
            $path = $_SERVER['DOCUMENT_ROOT'];
        }
        $combined_filemtime = (float)0;
        foreach($array as $path_file){
            //add up file modified time
            $combined_filemtime += @filemtime($path.$path_file);
        }
        $combined_file_browser_path = '/combined/'.$type.'/'.$combined_filemtime.'.min.'.$type;
        $combined_file_server_path = $path.$combined_file_browser_path;
        //if combined modified time not exists as a filename
        if(!file_exists($combined_file_server_path)){

            if (file_exists($path.'/combined/'.$type.'/')){
                self::deleteDir($path.'/combined/'.$type.'/');
            }

            mkdir($path.'/combined/'.$type, 0777, true);
            //create new combined file
            @ob_flush();
            ob_start();
            foreach($array as $path_file){
                include $path.$path_file;
            }


            if($type === 'css'){
                if( FALSE === file_put_contents($combined_file_server_path,
                    /* remove tabs, spaces, newlines, etc. */
                    str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '',
                        /* remove comments */
                        preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', ob_get_clean() )
                    ),
                    LOCK_EX
                )){
                    throw new toolboxException("Could not write to ".$combined_file_server_path, 1);
                }
            }elseif($type === 'js'){
                if( FALSE === file_put_contents($combined_file_server_path,
                     ob_get_clean(),
                    LOCK_EX
                )){
                    throw new toolboxException("Could not write to ".$combined_file_server_path, 1);
                }
            }else{
                if( FALSE === file_put_contents($combined_file_server_path,
                     ob_get_clean(),
                    LOCK_EX
                )){
                    throw new toolboxException("Could not write to ".$combined_file_server_path, 1);
                }
            }

            ob_get_clean();
        }

        //return the combined file
        return $combined_file_browser_path;
    }

    static function getHost(){
        $http_protocol = '';
        if(config::hasSetting('HTTP_PROTOCOL')){
            $http_protocol = config::getSetting('HTTP_PROTOCOL');
        }elseif(isset($_SERVER['HTTPS']) && isset($_SERVER['HTTPS']) == 'on'){
            $http_protocol = 'https';
        }elseif(isset($_SERVER['HTTPS']) && isset($_SERVER['HTTPS']) == 'off'){
            $http_protocol = 'http';
        }else{
            $http_protocol = 'http';
        }

        $http_host = '';
        if(config::hasSetting('HTTP_HOST')){
            $http_host = config::get()->getConfig('HTTP_HOST');
        }elseif(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != ''){
            $http_host = $_SERVER['HTTP_HOST'];
        }else{
            $http_host = gethostname();
        }

        if(trim($http_host) == ''){
            throw new toolboxException('Could not get server hostname, please set one in the config.', 1);
        }

        return $http_protocol.'://'.$http_host;
    }

    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new toolboxException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        return rmdir($dirPath);
    }

    static function utf8Encode($content) {
        return mb_convert_encoding($content, "UTF-8", mb_detect_encoding($content));
    }

    static function resize_image_netbpm($source_image_path, $thumbnail_image_path, $w = 150, $h = 150, $quality = 90){
        return exec('djpeg '.$source_image_path.' | pamscale -height '.$h.' | pamcut -width '
        .$h.' | cjpeg -optimize -progressive -quality '
        .$quality.' > '.$thumbnail_image_path);
    }

    public static function resize_image($source_image_path, $thumbnail_image_path, $w = 150, $h = 150, $quality = 90) {
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
            default: return;
        }
        if (empty($source_gd_image)) {
            return false;
        }


        $source_image_width = imagesx($source_gd_image);
        $source_image_height = imagesy($source_gd_image);

        if ($source_image_width <= $w && $source_image_height <= $h) {
            $w = $source_image_width;
            $h = $source_image_height;
        }


        $w_ratio = ($w / $source_image_width);
        $h_ratio = ($h / $source_image_height);

        if ($source_image_width > $source_image_height) {//landscape
            $crop_w = round($source_image_width * $h_ratio);
            $crop_h = $h;
        } elseif ($source_image_width < $source_image_height) {//portrait
            $crop_h = round($source_image_height * $w_ratio);
            $crop_w = $w;
        } else {//square
            $crop_w = $w;
            $crop_h = $h;
        }
        $dest_img = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($dest_img, 255, 255, 255);
        imagefill($dest_img, 0, 0, $white);
        imagecopyresampled($dest_img, $source_gd_image, 0, 0, 0, 0, $crop_w, $crop_h, $source_image_width, $source_image_height);
        imagejpeg($dest_img, $thumbnail_image_path, $quality);
        imagedestroy($dest_img);
        return true;
    }



    static function increaseStatCount($stat_name, $time_period){
        $store = store::get();
        //store the fact that a page was served
        $stats_array = $store->getValue($stat_name);
        if(!is_array($stats_array)){
            $stats_array = array();
        }

        //if array more than 60, remove oldest records
        if(count($stats_array) > 70){
            $del_val = end($stats_array);
            $del_key = key($stats_array);
            unset($stats_array[$del_key]);
            $store->deleteValue($stat_name.'['.$del_key.']');
        }

        //if time period exists, add to it
        if(isset($stats_array[$time_period])){
            //increase count
            $stats_array[$time_period] = true;
        }else{//else add to beginning of array
             $stats_array = array_merge(array($time_period => true), $stats_array);
        }

        $count = $store->increaseCounter($stat_name.'['.$time_period.']');
        $store->setValue($stat_name, $stats_array);

        return $count;


    }

    static function increaseStatAvg($stat_name, $time_period, $value){
        $store = store::get();

        $stats_array = $store->getValue($stat_name);
        if(!is_array($stats_array)){
            $stats_array = array();
        }

        //if array more than 60, remove oldest records
        if(count($stats_array) > 70){
            $del_val = end($stats_array);
            $del_key = key($stats_array);
            unset($stats_array[$del_key]);
            $store->deleteValue($stat_name.'['.$del_key.']');
        }

        //if time period exists, add to it
        if(isset($stats_array[$time_period])){
            //increase count
            $stats_array[$time_period] = true;
        }else{//else add to beginning of array
             $stats_array = array_merge(array($time_period => true), $stats_array);
        }

        $old_value = $store->getValue($stat_name.'['.$time_period.']');
        if($old_value != null){
            $value = ($old_value + $value)/2;
        }

        $store->setValue($stat_name.'['.$time_period.']', $value);
        $store->setValue($stat_name, $stats_array);

        return $value;
    }

    static function rebuildURL($get_params){
        $get_query = http_build_query($get_params);
        if($get_query != ''){
            $get_query = '?'.$get_query;
        }
        return $get_query;
    }

    static function mergeUrlParams($url, $new_params){
        $url_parts = parse_url($url);
        if(!isset($url_parts['query'])){
            $url_parts['query'] = '';
        }
        parse_str($url_parts['query'], $params);

        $params = array_merge($params, $new_params);

        // Note that this will url_encode all values
        $url_parts['query'] = http_build_query($params);

        // If not
        $new_url = '';
        if(isset($url_parts['scheme'])){
            $new_url .= $url_parts['scheme'] . '://';
        }

        if(isset($url_parts['host'])){
            $new_url .= $url_parts['host'];
        }

        return $new_url . $url_parts['path'] . '?' . $url_parts['query'];

    }

/*    static function increaseStatAvg($value, $stat_name, $time_period, $decimal_places = 3, $max_value = FALSE){

        //store the fact that a page was served
        $stats_array = apc_fetch($stat_name);
        if($stats_array === false){
            $stats_array = array();
        }

        //if array more than 60, remove oldest records
        if(count($stats_array) > 70){
            array_pop($stats_array);
        }

        //get last record
        $last_key = key($stats_array);
        //if last record matches current minute
        if($last_key === $time_period){
            //increase count
            //$stats_array[$last_key] = $stats_array[$last_key]+1;
            $array = array(
                'avg' => sprintf("%.".$decimal_places."f", ($stats_array[$last_key]['avg'] * $stats_array[$last_key]['count'] + $value)/($stats_array[$last_key]['count']+1)),
                'count' => ($stats_array[$last_key]['count'] + 1)
            );


            if($max_value !== false){
                if(!isset($stats_array[$last_key]['max']) || $stats_array[$last_key]['max'] < $value){
                    $array['max'] = $value;
                    $array['max_value'] = $max_value;
                }else{
                     $array['max'] = $stats_array[$last_key]['max'];
                     $array['max_value'] = $stats_array[$last_key]['max_value'];
                }
            }

            $stats_array[$last_key] = $array;


        }else{//else add to beginning of array
            $array = array('avg' => $value, 'count' => 1);
            if($max_value !== false){
                $array['max'] = $value;
                $array['max_value'] = $max_value;
            }
            $stats_array = array($time_period => $array) + $stats_array;
        }

        apc_store($stat_name, $stats_array);

    }*/

    static function getCronRunningInstanceCount($process_name){
        $output = array();
        $command = 'ps auxww| grep '.$process_name;

        exec($command, $output);
        $running = 0;

        foreach($output as $key => $value){
            if(strstr($value, "/usr/bin/php") !== FALSE){
                $running++;
            }
        }

        return $running;
    }

    static function secondsToReadable($seconds){
		$a_sec=1;
		$a_min=$a_sec*60;
		$an_hour=$a_min*60;
		$a_day=$an_hour*24;
		$a_week=$a_day*52;
		//$a_month=$a_day*(floor(365/12));
		$a_month=$a_day*30;
		$a_year=$a_day*365;
		$params=2;
		$text='';
		if($seconds>$a_year)
		{
			$years=floor($seconds/$a_year);
			$text.=$years.' years ';
			$seconds=$seconds-($years*$a_year);
			$params--;
		}
		if($params==0) return $text;
		if($seconds>$a_month)
		{
			$months=floor($seconds/$a_month);
			$text.=$months.' months ';
			$seconds=$seconds-($months*$a_month);
			$params--;
		}
		if($params==0) return $text;
		if($seconds>$a_week)
		{
			$weeks=floor($seconds/$a_week);
			$text.=$weeks.' weeks ';
			$seconds=$seconds-($months*$a_week);
			$params--;
		}
		if($params==0) return $text;
		if($seconds>$a_day)
		{
			$days=floor($seconds/$a_day);
			$text.=$days.' days ';
			$seconds=$seconds-($days*$a_day);
			$params--;
		}
		if($params==0) return $text;
		$H=ltrim(gmdate("H", $seconds), '0');
		if($H>0)
		{
			$text.=$H.' hours ';
			$params--;
		}
		if($params==0) return $text;
		$M=ltrim(gmdate("i", $seconds), '0');
		if($M>0)
		{
			$text.=$M.' minutes ';
			$params--;
		}
		if($params==0) return $text;
		$S=ltrim(gmdate("s", $seconds), '0');
		$text.=$S.' seconds ';

		return $text;

	}

    static function getStat($stat_name, $time_group = 'second'){

        $store = store::get();
        $array_indexes = $store->getValue($stat_name);
        if(!is_array($array_indexes)){
            $array_indexes = array();
        }

        if($time_group === 'second'){
            $current_sequential_time_group = date('Y-m-d H:i:s');
            $previous_sequential_time_group = date('Y-m-d H:i:s', time()-1);
        }elseif($time_group === 'minute'){
            $current_sequential_time_group = date('Y-m-d H:i:00');
            $previous_sequential_time_group = date('Y-m-d H:i:00', strtotime('-1 minute'));
        }elseif($time_group === 'hour'){
            $current_sequential_time_group = date('Y-m-d H:00:00');
            $previous_sequential_time_group = date('Y-m-d H:00:00', strtotime('-1 hour'));
        }else{
            throw new toolboxException('Invalid time grouping: '.$time_group, 1);
        }



        $array_values = array();
        $avg = 0;
        $count = 0;
        $first = null;
        foreach($array_indexes as $key => $value){
            $array_values[$key] = $store->getValue($stat_name.'['.$key.']');
            if($count !== 0){
                $avg += $array_values[$key];
            }
            $count++;
        }

        if(isset($array_values[$previous_sequential_time_group])){
            $previous_sequential = $array_values[$previous_sequential_time_group];
        }else{
            $previous_sequential = null;
        }
        if(isset($array_values[$current_sequential_time_group])){
            $current_sequential = $array_values[$current_sequential_time_group];
        }else{
            $current_sequential = null;
        }

        $avg = @round($avg / ($count-1));

        reset($array_values);

        return array(
            'current' => current($array_values),
            'current_sequential' => $current_sequential,
            'previous' => next($array_values),
            'previous_sequential' => $previous_sequential,//previous value without skipping time groups
            'avg' => $avg,//average without current value
            'values' => $array_values
        );

    }


    static function htmlEncode($html){
        return htmlspecialchars($html, ENT_QUOTES);
    }

    /*static function array2table($data){
        echo '<table class="table style1">';
            echo '<thead><tr>';
            foreach($data as $key => $value){
                echo '<th>'.$key.'</th>';
            }
            echo '</tr></thead>';
        echo '<tbody><tr>';
        foreach($data as $key => $value){
            echo '<td>'.$value.'</td>';
        }
        echo '</tr></tbody>';
        echo '</table>';
    }*/

    static function rows2table($data){
        $first = current($data);
        echo '<table class="table style1">';
            echo '<thead><tr>';
            foreach(get_object_vars($first) as $key => $value){
                echo '<th>'.$key.'</th>';
            }
            echo '</tr></thead>';
        echo '<tbody>';
        foreach($data as $key => $object){?>
            <tr><?php
            foreach($object as $property => $value){
                echo '<td>'.$value.'</td>';
            }?></tr><?php
        }
        echo '</tr></tbody>';
        echo '</table>';
    }

    static function array2table($data, $keys = null, $empty_value = '--', $style = 'style1'){
        if($keys === null){
            $keys = array_keys(current($data));
        }else{
            $keys = array_keys($keys);
        }
        echo '<table class="table '.$style.'">';
            echo '<thead><tr>';
            foreach($keys as $index => $key){
                echo '<th>'.$key.'</th>';
            }
            echo '</tr></thead>';
        echo '<tbody>';
        foreach($data as $key => $array){?>
            <tr><?php
            foreach($keys as $index => $field_name){
                if(isset($array[$field_name]))
                    echo '<td>'.$array[$field_name].'</td>';
                else
                    echo '<td>'.$empty_value.'</td>';
            }?></tr><?php
        }
        echo '</tbody>';
        echo '</table>';
    }

    static function array2tableV2($data, $keys = null, $empty_value = '--', $style = 'style1'){
        if(!isset($data[0])){
            $data_copy = $data;
            $data = array();
            $data[0] = $data_copy;
        }
        if($keys === null){
            $keys = array_keys($data[0]);
        }else{
            $keys = array_keys($keys);
        }
        echo '<table class="table '.$style.'">';
            echo '<thead><tr>';
            foreach($keys as $index => $key){
                echo '<th>'.$key.'</th>';
            }
            echo '</tr></thead>';
        echo '<tbody>';
        foreach($data as $row){ ?>
            <tr><?php
            foreach($keys as $key){
                    if(isset($row[$key]) && !empty($row[$key]))
                        echo '<td>'.self::array2string($row[$key]).'</td>';
                    else
                        echo '<td>'.$empty_value.'</td>';
            }
            ?></tr><?php
        }
        echo '</tbody>';
        echo '</table>';
    }

    static function arrayMergeSum(){
        $arrays = func_get_args();
        $keys = array_keys(array_reduce($arrays, function ($keys, $arr) { return $keys + $arr; }, array()));
        $sums = array();

        foreach ($keys as $key) {
            $sums[$key] = array_reduce($arrays, function ($sum, $arr) use ($key) { return $sum + @$arr[$key]; });
        }
        return $sums;
    }
    static function trimArray(&$array){
        foreach($array as $key => &$value){
            if(is_array($value)){
                self::trimArray($value);
            }else{
                $value = trim($value);
            }
        }
    }

    static function issetNotEmpty($index){
        if(isset($_POST[$index]) && trim($_POST[$index]) !== ''){
            return true;
        }

        return false;
    }


    static function array_to_xml( $data, &$xml_data = null) {
        if($xml_data === null){
            $xml_data = new \SimpleXMLElement('<xmlrequest></xmlrequest>');
        }
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    //$key = 'item'.$key; //dealing with <0/>..<n/> issues
                    $key = 'item';
                }
                $subnode = $xml_data->addChild($key);
                self::array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }

        return $xml_data->asXML();
    }

    static function xml_to_array($xmlstring) {
        $xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        return json_decode($json,TRUE);
    }

    static function emptyPlaceholder($value){
        if($value === '' || $value === ''){
            return '--';
        }

        return $value;
    }

    static function removeNewLines($string){
        return trim(preg_replace('/\s+/', ' ', $string));
    }

    static function trimNewLines($string){
        return str_replace(array("\n", "\r"), '', $string);
    }

    static function stringContains($original_string, $contains_array) {
        if(!is_array($contains_array)){
            $contains_array = array($contains_array);
        }

    	foreach ($contains_array as $key => $value) {
			if(strpos($original_string, $value) !== false){
				return true;
			}
		}

		return false;
    }

    static function stringStartsWith($original_string, $starts_with) {
        // search backwards starting from haystack length characters from the end
        return $starts_with === "" || strrpos($original_string, $starts_with, -strlen($original_string)) !== false;
    }

    static function stringEndsWith($original_string, $ends_with) {
        // search forward starting from end minus needle length characters
        return $ends_with === "" || (
            ($temp = strlen($original_string) - strlen($ends_with)) >= 0
            && strpos($original_string, $ends_with, $temp) !== false
        );
    }

    static function plural($count){
        if($count == 1){
            return '';
        }else{
            return 's';
        }
    }

    static function isPost(){
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? true : false;
    }

    static function domain2SLD($domain_name){
        $domain_parts = utils::parseUrl($domain_name);
        $sld = utils::removeStringFromEnd($domain_parts['domain'], '.'.$domain_parts['extension']);
        $tld = utils::removeStringFromBeginning($domain_parts['extension'], '.');

        return $sld;
    }

    static function stringToArray($string, $key_value_separator, $element_separator){
        $string = explode($element_separator, $string);
        $array = array();
        foreach ($string as $value) {
            $element = explode($key_value_separator, $value);
            if(isset($element[1])){
                $array[$element[0]] = $element[1];
            }else{
                $array[$element[0]] = null;
            }
        }

        return $array;

    }

    static function sendEmail($email_address, $subject_line, $body_content, $from = null, $plaintext = false){
        if($plaintext){
            $linebreak = "\n";
            $content_type = 'text/plain';
        }else{
            $linebreak = '<br>';
            $content_type = 'text/html';
        }

        if(is_array($email_address)){
            $email_to = implode(', ', $email_address);
        }else{
            $email_to = $email_address;
        }

		if($from === null){
			$from = "From: ".config::get()->getConfig('app_name')." <support@".config::get()->getConfig('HTTP_HOST').">\r\n"
                    . 'MIME-Version: 1.0' . "\r\n"
                    . 'Content-type: '.$content_type.'; charset=UTF-8';
		}

        mail($email_to,
                $subject_line,
                $body_content,
                $from);

    }

    static function sendEmailPlaintext($email_address, $subject_line, $body_content, $from = null){
        self::sendEmail($email_address, $subject_line, $body_content, $from, true);
    }

    static function array2delimited($array, $key_value_delimiter = ' => ', $element_delimiter = "\n"){
        if(is_string($array)){
            return $array;
        }

        if($array === null){
            return 'NULL';
        }

        if(!is_array($array)){
            return var_export($array, true);
        }

        $output = "".$element_delimiter;

        foreach($array as $key => $value){

            $output .= $key .$key_value_delimiter
                    . self::array2delimited($value, $key_value_delimiter, $element_delimiter."\t").$element_delimiter;

        }

        return $output;
    }

    static function getStringBetweenTwoStrings($string, $start_string, $end_string){
        $string = ' ' . $string;
        $ini = strpos($string, $start_string);
        if ($ini == 0) return '';
        $ini += strlen($start_string);
        $len = strpos($string, $end_string, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    static function isCrossDomain(){
        if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], utils::getHost()) !== 0){
            return true;
        }

        return false;
    }

    static function getAllHeaders(){
        if(!function_exists('getallheaders')){
            $headers = array();
            foreach($_SERVER as $name => $value){
                if(substr($name, 0, 5) == 'HTTP_'){
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }

        return getallheaders();
    }

    static function varReplace($str, $array){
        $vars = array();
        $vals = array();
        foreach ($array as $key => $value) {
            $vars[] = '{'.$key.'}';
            //$vals[] = '<span class="notifications style1"><span class="typeof">'.gettype($value).'</span> '.utils::htmlEncode(var_export($value, true)).'</span>';
            $vals[] = '<span class="notifications style1">'.utils::htmlEncode($value).'</span>';
        }

        return str_replace($vars, $vals, $str);

    }

    static function logNonFatalError($string_or_exception){
        if(is_string($string_or_exception)){

            try{
                throw new toolboxException($string_or_exception, 1);
            }catch(toolboxException $e){
                errorHandler::logNonFatalError($e);
            }

        }else{
            errorHandler::logNonFatalError($string_or_exception);
        }
    }

    static function string2ellipsis($string, $limit){
        if(strlen($string) > $limit){
            return substr($string, 0, $limit).'...';
        }

        return $string;

    }

    static function removeStringAfterString($str, $after){
        return substr($str, 0, strpos($str, $after));
    }

    private static $count = 0;
    static function incCount($key){
        if(!isset(self::$count)){
            self::$count = 0;
        }

        return self::$count++;
    }

    static function array_rand($array){
        $rand_key = array_rand($array);
        return $array[$rand_key];
    }

    static function isAdminIP($ip){
        $admin_ips = array();
        if(is_array(config::getSetting('admin_ip'))){
            $admin_ips = config::getSetting('admin_ip');
        }else{
            $admin_ips[] = config::getSetting('admin_ip');
        }

        return in_array($ip, $admin_ips);

    }
    /*
    'prefix' is the start of the CC number as a string, any number of digits.
    'length' is the length of the CC number to generate. Typically 13 or 16
    */
    static function generateCreditCard($prefix = '', $length = 16) {

        $ccnumber = $prefix;
        # generate digits
        while ( strlen($ccnumber) < ($length - 1) ) {
            $ccnumber .= rand(0,9);
        }


        # Calculate sum
        $sum = 0;
        $pos = 0;
        $reversedCCnumber = strrev( $ccnumber );
        while ( $pos < $length - 1 ) {
            $odd = $reversedCCnumber[ $pos ] * 2;
            if ( $odd > 9 ) {
                $odd -= 9;
            }
            $sum += $odd;
            if ( $pos != ($length - 2) ) {
                $sum += $reversedCCnumber[ $pos +1 ];
            }
            $pos += 2;
        }
        # Calculate check digit
        $checkdigit = (( floor($sum/10) + 1) * 10 - $sum) % 10;
        $ccnumber .= $checkdigit;
        return $ccnumber;

	}


    static function daysBetween($start, $end = null){
        if($end === null){
            $end = time();
        }
        $date1 = new \DateTime(date('Y-m-d H:i:s', $start));
        $date2 = new \DateTime(date('Y-m-d H:i:s', $end));

        $diff = (int)$date2->diff($date1)->format("%a");

        return $diff;
    }

    static function ifNull(&$var, $val){
        if($var === null){
            $var = $val;
        }
    }

    static function encrypt($data, $encryption_key, $initialization_vector = null){
    	if($initialization_vector === null){
    		$initialization_vector = config::getSetting('encryption_salt');
    	}
		return openssl_encrypt($data, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $initialization_vector);
    }

    static function decrypt($data, $encryption_key, $initialization_vector = null){
    	if($initialization_vector === null){
    		$initialization_vector = config::getSetting('encryption_salt');
    	}
		return openssl_decrypt($data, 'aes-256-cbc', $encryption_key, OPENSSL_RAW_DATA, $initialization_vector);
    }

	static function diff($old, $new){
		if(is_string($old)){
			$old = explode(' ', $old);
		}

		if(is_string($new)){
			$new = explode(' ', $new);
		}

		$maxlen = 0;
		foreach($old as $oindex => $ovalue){
			$nkeys = array_keys($new, $ovalue);
			foreach($nkeys as $nindex){
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
					$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
				if($matrix[$oindex][$nindex] > $maxlen){
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}
		}
		if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
		return array_merge(
			self::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			self::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
	}

	static function htmlDiff($old, $new){
		$diff = self::diff($old, $new);
		$ret = '';
		foreach($diff as $k){
			if(is_array($k))
				$ret .= (!empty($k['d'])?"<del>".str_replace("\n", '<<<delnl>>>', implode(' ',$k['d']))."</del> ":'').
					(!empty($k['i'])?"<ins>".str_replace("\n", '<<<insnl>>>', implode(' ',$k['i']))."</ins> ":'');
			else $ret .= $k . ' ';
		}
		return $ret;
	}

}