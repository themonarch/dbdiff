<?php
namespace toolbox;
class formatter{

    static function percent_sign($num){
    	if($num == 'NA'){
    		return  $num;
    	}
        return $num.'%';
    }

    /**
     * round number to specified percision.
     * @param $padding true = pad numbers with zeros to fill precision
     */
    static function round($num, $precision, $padding = false){
        if($padding === true){
            return self::decimal_padding(round($num, $precision), $precision);
        }else{
            return round($num, $precision);
        }
    }

    /**
     * pad numbers with trailing zeros to fill up decimal places
     */
    static function decimal_padding($num, $decimals){
        $decimal_count = utils::countDecimals($num);
        if($decimal_count === 0 && $decimal_count < $decimals){
            $num .= '.';
        }
        for ($i=$decimal_count; $i < $decimals; $i++) {
            $num .= '0';
        }

        return $num;
    }

    static function moz_spam_flag($flag){
        if($flag === '0'){
            $flag = '<span style="color: #66BC00;">Good</span>';
        }elseif($flag === '1'){
            $flag = '<span style="color: #d70202;">Poor</span>';
        }else{
            $flag = '<span style="cursor: pointer; font-size: 14px;"
            			data-overlay-id="why_missing_moz_spam_flag"
            			data-ajax_url="/ajax/overlay/why_missing_moz_spam_flag">
                        		No Data Yet (<a style="padding: 0 2px;" href="#">why?</a>)
                    		</span>';
        }
        return $flag;
    }

    /**
     * Add commas between thousands places, preserving original decimals.
     */
    public static function number_commas($num, $digits_before_round = false) {
        $exp = explode('.', $num);

        if(count($exp) > 1){//has decimals
            $digits = $exp[0];
            if($digits_before_round !== false && strlen($digits) > $digits_before_round){//has more than X digits, round
                $round = 0;
            }else{
                $round = strlen($exp[1]);
            }
        }else{
            $round = 0;
        }

        return number_format($num, $round);
    }

    public static function price($num) {
        return '$'.self::number_commas(formatter::roundTwoDec($num));
    }

    public static function str_to_date($date) {
        return date('F Y', strtotime($date));
    }

    public static function unix_to_date($date) {
        return date('F Y', $date);
    }

    public static function roundTwoDec($value) {
        return formatter::round($value, 2, true);
    }

    /**
     * Add commas between thousands places, preserving original decimals.
     */
    public static function times100($num) {
        return $num*100;
    }


    public static function strtotime($date) {
        return strtotime($date);
    }

    public static function url($url) {
        $url = utils::removeStringFromBeginning($url, 'http://');
        $url = utils::removeStringFromBeginning($url, 'https://');
        $url = utils::removeStringFromBeginning($url, 'www.');
		return $url;
    }

    /**
     * see similarweb paid vs organic terms metric
     */
    public static function split_rows($value, $field) {
    	$new_rows = array();
        $split_fields = json_decode($value->$field);
		if(empty($split_fields)){
			return null;
		}
		foreach($split_fields as $split_field){
			$row_clone = clone $value;
			$row_clone->$field = $split_field;
			$new_rows[] = $row_clone;
		}
		return $new_rows;
    }



    static function locationArray($row){
        $location = '';
        if($row['city_name'] != ''){
            $location .= $row['city_name'].', ';
        }elseif($row['subdivision_1_name'] != ''){
            $location .= $row['subdivision_1_name'].', ';
        }
        if($row['country_name'] != ''){
            $location .= $row['country_name'];
        }

        if($location === '' && isset($row['continent_name'])){
            $location = 'unknown location in '.$row['continent_name'];
        }elseif($location === ''){
            $location = 'unknown location';
        }
        //utils::removeStringFromEnd($location, ', ');

        return $location;

    }

    static function location($row){
        $location = '';
        if($row->city_name != ''){
            $location .= $row->city_name.', ';
        }
        if($row->subdivision_1_name != ''){
            $location .= $row->subdivision_1_name.', ';
        }
        if($row->country_name != ''){
            $location .= $row->country_name;
        }elseif(isset($row->country_iso_code) && $row->country_iso_code != ''){
            $location .= $row->country_iso_code;

        }

        if($location === '' && isset($row->continent_name)){
            $location = 'unknown location in '.$row->continent_name;
        }elseif($location === '' ){
            $location = 'unknown location';
        }

        //utils::removeStringFromEnd($location, ', ');

        return $location;


    }

    static function dateTypeDisplay($date_type, $range_start_timestamp, $range_end_timestamp = null){
        if($date_type === 'day'){
            return date('M. d, Y', $range_start_timestamp);
        }

        if($date_type === 'week'){
            return date('M. d, Y', $range_start_timestamp) . ' thru '.date('M. d, Y', $range_end_timestamp);
        }

        if($date_type === 'month'){
            return date('M, Y', $range_start_timestamp);
        }

        return false;

    }

	static function bytes($bytes, $precision = 2){
	    $units = array('B', 'KB', 'MB', 'GB', 'TB');

	    $bytes = max($bytes, 0);
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	    $pow = min($pow, count($units) - 1);

	    // Uncomment one of the following alternatives
	    // $bytes /= pow(1024, $pow);
	     $bytes /= (1 << (10 * $pow));

	    return formatter::number_commas(round($bytes, $precision)) . ' ' . $units[$pow];
	}


}

class emptyRow extends toolboxException {}
