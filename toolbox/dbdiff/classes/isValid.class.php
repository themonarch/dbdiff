<?php
namespace toolbox;
/**
 * validation methods
 */
 class isValid{

    /**
     * validate email
     */
    public static function email($email){
        $email = trim($email);
        if($email == ''){
            return 'Email address is missing!';
        }


        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email address was invalid!';
        }

        return true;
    }

    public static function fullname($value){
        $value = trim($value);
        if($value == '' || count(explode(' ', $value)) < 2){
            return 'Please enter a full name (first and last name).';
        }

        return true;
    }

    public static function phone($value){
        $regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
        return preg_match( $regex, $value ) ? true : 'Please enter a valid phone number.';
    }

    public static function domain($domain){
        if(!(preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
            && preg_match("/^.{1,253}$/", $domain) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain))){ //length of each label){

            return 'Website entered is not a valid domain name.';
        }



        try{
            $parsed_url = utils::parseUrl($domain);
            //$domain_name = utils::removeStringFromBeginning($parsed_url['host'], 'www.');
        }catch(toolboxException $e){
            return 'Website is not a valid domain name.';
        }

        if(!isset($parsed_url['host'])){
            return 'Website is not a valid domain name.';
        }

        return true;
    }

    public static function ip_address($value){

        if(filter_var($value, FILTER_VALIDATE_IP)){
            return true;
        }

        return 'Please enter a valid ip address.';

    }

    public static function boolean($value){
        if($value === '1' || $value === '0'){
            return true;
        }

        return 'Invalid boolean value.';

    }

    public static function yesNo($value){
        if($value === 'yes' || $value === 'no'){
            return true;
        }

        return 'Please choose a valid option.';

    }

    public static function general($value){
        $value = trim($value);
        if($value == ''){
            return 'Please enter a value for this field.';
        }

        return true;
    }

    public static function password($value){
        $value = trim($value);
        if($value == ''){
            return 'Please enter a password that is 6 to 50 characters long.';
        }

        $len = strlen($value);
        if($len < 6 || $len > 50){
            return 'Password must be between 6 and 50 characters!';
        }

        return true;
    }

    public static function password2($value){
        if($_POST['password'] !== $value){
            return 'Passwords did not match!';
        }

        return true;
    }

    public static function credit_card($cc_number) {
        $card_type = appUtils::getCardType($cc_number);
        if($card_type === false){
            return 'This card is not valid.';
        }


        if(config::get()->isSetConfig('supported_card_types')){
            $card_types = array_map('strtolower', config::get()->getConfig('supported_card_types'));

            if(!in_array($card_type, $card_types)){
                return 'Sorry we do not support this type of card. We only support: '
                    .ucwords(implode(', ', config::get()->getConfig('supported_card_types')));
            }
        }

        return true;
    }

    static function cvv($cvv){
        if(strlen($cvv) < 3 || strlen($cvv) > 4 || !is_numeric($cvv)){
            return 'CVV code ('.$cvv.') is invalid format.';
        }
        return true;
    }

    static function card_month_and_year($month, $year = null){
        $msg = 'Please enter a valid expiration date';
        if(!is_numeric($month) || strlen($month) !== 2){
            return $msg;
        }

        $month = (int)$month;

        if($month > 12 || $month < 1){
            return $msg;
        }

        if($year === null){
            $year = $_POST['card_year'];
        }

        if(!is_numeric($year)){
            return $msg;
        }

        $year = (int)$year;

        if(strtotime('last day of '.$month.'/01/'.$year) < time()){
            return 'This card is expired.';
        }

        return true;
    }

    static function card_year_and_month($year){
        $month = $_POST['card_month'];

        $msg = 'Please enter a valid expiration date';
        if(!is_numeric($month) || strlen($month) !== 2){
            return $msg;
        }

        $month = (int)$month;

        if($month > 12 || $month < 1){
            return $msg;
        }


        if(!is_numeric($year)){
            return $msg;
        }

        $year = (int)$year;

        if(strtotime('last day of '.$month.'/01/'.$year) < time()){
            return 'This card is expired.';
        }

        return true;
    }


    static function domain_and_tld($domain){
        if(
            !isset($domain)
            || $domain == ''
        ){
            return 'Please enter a domain name.';
        }

        $dot_count = substr_count($domain, '.');
        if($dot_count === 1){
            $parts = explode('.', $domain);
            $domain = $parts[0];
            $_POST['tld'] = '.'.$parts[1];
            if(strpos($_POST['tld'], '/') !== false){
                return 'Please enter a domain name without any paths.';
            }
            $_POST['domain'] = utils::removeStringFromEnd($_POST['domain'], $_POST['tld']);
        }elseif($dot_count > 1){
            return 'Please enter a valid domain name without any subdomains.';
        }

        if(!in_array($_POST['tld'], appUtils::getAllowedTLDs())){
            return 'The extension '.utils::htmlEncode($_POST['tld']).' is not allowed at this time.';
        }

        return self::domain($domain.$_POST['tld']);

    }

    static function region_manual($region){
        if(isset($_POST['region']) && $_POST['region'] == 'enter_manually'){
            return self::general($region);
        }

        return true;
    }


    static function checkbox($value){
        $value = trim($value);
        if($value == ''){
            return 'Please check the box to continue.';
        }

        return true;
    }
}

