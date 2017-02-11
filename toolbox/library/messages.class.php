<?php
namespace toolbox;
/**
 * flash messages
 */
class messages {
    private static $messages = array();
    private static $sessionMessageNames = array();
    private static $count = 0;
    function __construct() {
        return false;
    }

    public static function setErrorMessage($msg, $name = 'messages'){
        if(trim($msg) != ''){
            $cookie = new cookieHelper();
            $cookie->setCookieName($name.'[error]['.self::$count++.']');
            $cookie->setCookieValue($msg);
            $cookie->sendCookieToUser();
            self::$sessionMessageNames[] = $name;
        }

    }

    public static function setWarningMessage($msg, $name = 'messages'){
        if(trim($msg) != ''){
            $cookie = new cookieHelper();
            $cookie->setCookieName($name.'[warning]['.self::$count++.']');
            $cookie->setCookieValue($msg);
            $cookie->sendCookieToUser();
            self::$sessionMessageNames[] = $name;
        }

    }

    public static function setSuccessMessage($msg, $name = 'messages'){
        if(trim($msg) != ''){
            $cookie = new cookieHelper();
            $cookie->setCookieName($name.'[success]['.self::$count++.']');
            $cookie->setCookieValue($msg);
            $cookie->sendCookieToUser();
            self::$sessionMessageNames[] = $name;
        }
    }

    public static function setCustomMessage($msg, $name = 'messages', $type = 'default', $array_key = false){
        $cookie = new cookieHelper();
        if($array_key !== false){
            $cookie->setCookieName($name.'['.$type.']['.$array_key.']');
        }else{
            $cookie->setCookieName($name.'['.$type.']');
        }
        $cookie->setCookieValue($msg);
        $cookie->sendCookieToUser();
        self::$sessionMessageNames[] = $name;
    }

    public static function setWrong($msg, $name, $array_key = false){
        self::setCustomMessage($msg, 'wrong', $name, $array_key);
    }

    /**
     * Read all messages set within this execution process.
     */
    public static function readSessionMessages(){
        foreach(self::$sessionMessageNames as $name){
            self::readMessages($name);
        }
    }

    /**
     * Gets messages from cookies and clears to cookies.
     */
    public static function readMessages($name = 'messages', $type = null){
        $cookie = cookieHelper::get();
        if(isset(self::$messages[$name])){
            self::$messages[$name] = array_merge(self::$messages[$name], $cookie->getCookie($name, true));
        }else{
            self::$messages[$name] = $cookie->getCookie($name, true);
        }
        if($type !== null){
            if(isset(self::$messages[$name][$type])){
                return self::$messages[$name][$type];
            }else{
                return null;
            }
        }

        return self::$messages[$name];
    }

    /**
     * Gets messages from cookies and clears to cookies.
     */
    public static function printMessages($name = 'messages', $style = 'style1'){
        $messages = self::readMessages($name);
        if( count($messages) == 0){
            return false;
        }

        foreach($messages as $type=>$message_group){
            if(count($message_group) > 0){
                messages::output($message_group, $type, $style);
            }
        }

        return true;
    }

    static function output($message, $type, $style = 'style1'){
        ?><div class="clear-sidebar"><!--
            --><div class="messages-container <?php echo $style; ?>"><!--
            --><div class="messages messages-<?php echo $type; ?>"><?php
            if(is_array($message)){
                foreach ($message as $key => $msg) { ?><!--
            --><div class="message"><?php echo $msg; ?></div><!--
            --><?php }
            }else{ ?><!--
            --><div class="message"><?php echo $message; ?></div><!--
            --><?php } ?><!--
            --></div><!--
            --></div><!--
            --></div><?php
    }

}
