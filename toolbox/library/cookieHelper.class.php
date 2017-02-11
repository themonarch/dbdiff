<?php
namespace toolbox;
/**
 *
 */
class cookieHelper {
    private $cookieName;
    private $cookieValue;
    private $cookieExpiration = 0;
    private $cookieDomainScope = '';
    private $cookiePathScope = '/';
    private static $sentCookies = array();

    function __construct(){

    }

    function setCookieName($cookieName){
        $cookieName = trim($cookieName);

        if($cookieName == '')
            throw new cookieException('Cannot create a cookie with no name in cookieHelper class!');

        $this->cookieName = $cookieName;
        return $this;
        return new cookieHelper();
    }

    function setCookieValue($cookieValue){
        $cookieValue = trim($cookieValue);

        $this->cookieValue = $cookieValue;
        return $this;
        return new cookieHelper();
    }

    /**
     * Destroys all records of cookie (user side and php side)
     */
    function destroyCookie($cookieName){
        $cookieName = trim($cookieName);
        if($cookieName == '')
           throw new cookieException('Cannot destroy cookie with no name in cookieHelper class!');

        unset($_COOKIE[$cookieName]);
        if(headers_sent())
            return;
        setcookie($cookieName, null, -1, '/');
        $raw_cookies = array();
        if(isset($_SERVER['HTTP_COOKIE'])){
            $raw_cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        }
        foreach ($raw_cookies as $raw_cookie) {
            $raw_cookie = trim($raw_cookie);
            if(strpos($raw_cookie, $cookieName.'[') === 0){
                $raw_cookie_arr = explode('=', $raw_cookie);
                setcookie($raw_cookie_arr[0], null, -1, '/');
            }
        }

        foreach(self::$sentCookies as $key=>$sent_cookie){

            if(strpos($sent_cookie, $cookieName.'[') === 0){
                setcookie($sent_cookie, null, -1, '/');
            }
        }



        return $this;
        return new cookieHelper();
    }

    function setCookieExpirationToUnixTimestamp($unixtime){
        if(!utils::isValidTimeStamp($unixtime))
            throw new cookieException('Invalid timestamp given for cookieHelper class ( given: '
                            .$unixtime.')');
        $this->cookieExpiration = $unixtime;

        return $this;
        return new cookieHelper();
    }

    function setCookieExpirationToDays($days){
        $days = (int)$days;
        $this->cookieExpiration = time()+60*60*24*$days;
        return $this;
        return new cookieHelper();
    }

    function setCookieExpirationToHours($hours){
        $hours = (int)$hours;
        $this->cookieExpiration = time()+60*60*$hours;
        return $this;
        return new cookieHelper();

    }


    function setCookieExpirationToMinutes($minutes){
        $minutes = (int)$minutes;
        $this->cookieExpiration = time()+60*$minutes;
        return $this;
        return new cookieHelper();

    }

    function setCookieExpirationToSeconds($seconds){
        $seconds = (int)$seconds;
        $this->cookieExpiration = time()+$seconds;
        return $this;
        return new cookieHelper();
    }

    function setCookieExpirationToEndOfSession(){
        $this->cookieExpiration = 0;
        return $this;
        return new cookieHelper();
    }


    /**
     * By default, the cookies are scoped all paths on domain. Use this
     * to make your cookie readable only on specific paths. EX: /admin/
     */
    function setCookiePathTo($path){
        $path = trim($path);

        if($path == '')
            throw new cookieException('Cannot create a cookie with no path value in cookieHelper class!');
        if(substr($path, 0, 1) !== '/')
            throw new cookieException('Cookie path must start with a forward-slash!');

        $this->cookiePathScope = $path;

        return $this;
        return new cookieHelper();
    }

    /*TODO: finish this to find current PATH from url (not including parameters)
    function setCookiePathToCurrentPath(){
        $this->setCookiePathTo($_SERVER['REQUEST_URI']);
        return $this;
        return new cookieHelper();
    }*/


    /**
     * By default, cookies are only set for current subdomain (e.g: WWW.example.com).
     * Use this to automatically make cookie available to host domain and any subdomains.
     */
    function setCookieDomainToAllSubdomains(){
        $url = 'https://'.$_SERVER['HTTP_HOST'].'/';
        $parsed_url = utils::parseUrl($url);
        if(trim($parsed_url['domain']) == ''){
            throw new cookieException('Could not extract domain from the url: '. $url);
        }
        $this->setCookieDomainTo('.'.$parsed_url['domain']);
        return $this;
        return new cookieHelper();
    }

    /**
     * By default, cookies are only set for current subdomain (e.g: WWW.example.com).
     * Use this to set which subdomain for cookie to be avaliable on.
     */
    function setCookieDomainTo($domain){
        $this->cookieDomainScope = $domain;
    }

    /**
     * Tries to send current cookie to user.
     * If headers already sent this will throw an exception.
     */
    function sendCookieToUser(){
        $this->preSendChecks();

        $result = setcookie(
                        $this->cookieName,
                        $this->cookieValue,
                        $this->cookieExpiration,
                        $this->cookiePathScope,
                        $this->cookieDomainScope
                    );

        if(!$result)
            throw new cookieException('Could not send cookie in cookieHelperClass: '.utils::array2string(array($this->cookieName, $this->cookieValue, $this->cookieExpiration, $this->cookiePathScope,
                $this->cookieDomainScope)));

        self::$sentCookies[] = $this->cookieName;
        $temp_cookie_array = array();
        parse_str(urlencode($this->cookieName) . '=' . urlencode($this->cookieValue), $temp_cookie_array);
        $_COOKIE = array_replace_recursive($_COOKIE, $temp_cookie_array);
        unset($this->cookieName);
        unset($this->cookieValue);
        $this->cookiePathScope = '/';
        $this->cookieDomainScope = '';

        return true;

    }

    private function preSendChecks(){
        if(headers_sent())
            throw new cookieException('Cannot create a cookie after headers have been sent!');
        if(!isset($this->cookieName))
            throw new cookieException('No cookie name set in cookieHelper class!');
        if(!isset($this->cookieValue))
            throw new cookieException('No cookie value set in cookieHelper class!');

    }



    /**
     * Get cookie that was just sent OR received by name
     *
     */
    function getCookie($cookie_name, $delete = false){

        if(isset($_COOKIE[$cookie_name])){
            $cookie = $_COOKIE[$cookie_name];
            if($delete){
                $this->destroyCookie($cookie_name);
            }
        }else{
            $cookie = array();
        }

        return $cookie;

    }


    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new cookieHelper();
    }

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instance;
    public static function get(){
        if (!isset(self::$instance)) {
            self::$instance = new cookieHelper();
        }
        return self::$instance;
        return new cookieHelper();
    }
}

class cookieException extends toolboxException {}