<?php
namespace toolbox;
class errorHandler {

    /**
     * Pass a FATAL exception to the error handler
     */
    static function passError($e){
        errorHandler::get()->handleError($e, true);
    }

    /**
     * Default exception handler for all uncaught exceptions
     */
    static function exceptionHandler($e){
        toolbox::$error_handled = true;//tell the shutdown function we already handled the error.
        errorHandler::get()->handleError($e, true);
    }

    /**
     * Pass a NONFATAL exception to the error handler
     */
    static function logNonFatalError($e){
        errorHandler::get()->handleError($e, false);
    }

    private $emails = array();
    /**
     * Add/update an email to recieve the error.
     */
    function addEmail(
        $email_address,
        $notify_about_fatal_errors = true,
        $notify_about_nonfatal_errors = true
    ){

        $this->emails[$email_address] = array(
            'fatal' => $notify_about_fatal_errors,
            'nonfatal' => $notify_about_nonfatal_errors
        );

        return $this;
        return new errorHandler();

    }

    function clearEmails(){
        $this->emails = array();

        return $this;
        return new errorHandler();
    }

    private $no_log_exceptions = array();
    /**
     * Set an exception to not be logged (neither file nor email)
     * @param $class_name string full name of exception class including namespace
     */
    function setExceptionNoLogging($class_name){

        $this->no_log_exceptions[] = $class_name;

        return $this;
        return new errorHandler();

    }

    private $whitelisted_error_messages = array();
    /**
     * Set an error message to not be logged nor attempt to exit.
     * @param $msg string exact text of the message to ignore
     */
    function setMessageWhitelisted($msg){

        $this->whitelisted_error_messages[] = $msg;

        return $this;
        return new errorHandler();

    }

    private $whitelisted_classes = array();
    /**
     * Set an exception class to not be logged nor attempt to exit.
     * @param $msg string full name of exception's class (including namespace)
     */
    function setClassWhitelisted($class_name){

        $this->whitelisted_classes[] = $class_name;

        return $this;
        return new errorHandler();

    }

    private $times_called_fatal = 0;

    /**
     * Process the exception.
     */
    function handleError($e, $fatal = true){

        $class_name = get_class($e);
        $time_of_error = date('Y-m-d H:i:s');

        if(//ignore error if...
            in_array($e->getMessage(), $this->whitelisted_error_messages)//msg is whitelisted
            || in_array($class_name, $this->whitelisted_classes)//exception class is whitelisted
        ){
            return;
        }

        if($fatal){//incase of recursive fatal error, we must end here.
            $this->times_called_fatal++;
            if($this->times_called_fatal > 2){
                die('<script></script><table></table>There was an unexpected recursive error. '
                .'Please try again or come back at a later time.');
            }
        }

        //set the url
        if(!isset($_SERVER['SERVER_NAME']) || $_SERVER['SERVER_NAME'] == ''){
            $url = gethostname().'/'.router::get()->getUri();
        }elseif(isset($_SERVER['HTTPS']) && isset($_SERVER['HTTPS']) == 'on'){
            $url = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }else{
            $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }

        //create a readable stack trace
        $stack_trace = '';
        $db = false;
        foreach (array_reverse($e->getTrace()) as $key => $value) {
            if(isset($value['file']))
                $stack_trace .= "\n\t".'File #'.$key.': '.$value['file'].' ('.$value['line'].')';
            if(isset($value['class'])){
                if($value['class'].$value['type'].$value['function'] == 'toolbox\db::connect'){
                    $db = true;
                }
                $stack_trace .= "\n\t\t".$value['class'].$value['type'].$value['function'].'(';
            }elseif(isset($value['function']))
                $stack_trace .= "\n\t\t".$value['function'].'(';
            if(isset($value['args'])){
                if($db && isset($value['args'][2])){
                    $db = false;
                    $value['args'][2] = '******';
                }
            foreach ($value['args'] as $key => $value) {
                if(is_object($value)){
                    $stack_trace .= str_replace("\n", "\n\t\t\t", 'Array[size:'.count($value).']('
                        .utils::string2ellipsis(utils::array2string($value), 100).')').', ';
                }elseif(is_array($value)){
                    $stack_trace .= str_replace("\n", "\n\t\t\t", 'Array[size:'.count($value).']('
                        .utils::string2ellipsis(utils::array2string($value), 100).')').', ';
                }else{
                    $stack_trace .= str_replace("\n", "\n\t\t\t", '"'.utils::array2string($value)).'"'.', ';
                }
            }
            }

            $stack_trace = utils::removeStringFromEnd($stack_trace, ', ');
            $stack_trace .= ')';
        }

        $stack_trace = trim($stack_trace);

        if(//log error only if ...
            !in_array($class_name, $this->no_log_exceptions)//error class can be logged
        ){

            try{//notify the admins
                $this->emailException($e, $stack_trace, $fatal, $time_of_error, $url);
            }catch(\Exception $e){
                //log that we couldn't email the error
                $this->logExeption($e, '', false, time(), $url);
            }

            $this->logExeption($e, $stack_trace, $fatal, $time_of_error, $url);

        }

        if($fatal){//exit if fatal

            //call any callback functions
            foreach($this->callback_functions as $callback_function){
                if(is_callable($callback_function)){
                    call_user_func($callback_function, $e);
                }
            }

            die('<script></script><table></table><span style="color: red;">There was an unexpected error. '
                .'Details of this issue have been logged. '
                .'Please try again or come back at a later time.</span>');
        }

    }

    public function emailException($e, $stack_trace, $is_fatal, $time_of_error, $url){

        if(empty($this->emails)){
            return false;
        }
        $emails = array();
        foreach ($this->emails as $email => $settings) {
            if($is_fatal && $settings['fatal'] === false){
                continue;//don't email fatal error
            }

            if(!$is_fatal && $settings['nonfatal'] === false){
                continue;//don't email non-fatal error
            }

            $emails[] = $email;
        }

        $subject = 'Non-Fatal Error: '.substr($e->getMessage(), 0, 50);
        if($is_fatal){
            $subject = 'Fatal Error: '.substr($e->getMessage(), 0, 50);
        }

        $body = "\n".'========================================================='
                        ."\n".($is_fatal ? '[FATAL]' : '[NON-FATAL]'). ' [' . $time_of_error . ']: '.get_class($e)
                        ."\n".'========================================================='
                        ."\n".'ERROR MSG: ' .$e->getMessage()
                        ."\n".'------------------------------------------------------------------------------------------------------------------'
                        ."\n".'URL: '.$url
                        ."\n".'IP: '.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '')
                        ."\n".'FILE: '.$e->getFile() . ' ('.$e->getLine().')'
                        ."\n".'TRACE: '.$stack_trace
                        ."\n";

        utils::sendEmailPlaintext($emails, $subject, $body);

    }

    public $callback_functions = array();
    function addCallback($function){
        $this->callback_functions[] = $function;
        return $this;
        return new self();
    }

    private $logFile = '../toolbox/error.log';
    /**
     * Set path to log file or set false to disable logging to file.
     */
    function setErrorLogFile($logFile){
        //TODO: throw exception if log file not writabe.
        $this->logFile = $logFile;
        return $this;
        return new self();
    }

    private function logExeption($e, $stack_trace, $is_fatal, $time, $url){
        if($this->logFile === false){
            return;
        }

        try{
            if(!file_exists(toolbox::getPath().'/'.$this->logFile)){
                $fp = fopen(toolbox::getPath().'/'.$this->logFile, 'w');
            }

            file_put_contents(
                toolbox::getPath().'/'.$this->logFile,
                    "\n".'========================================================='
                        ."\n".($is_fatal ? '[FATAL]' : '[NON-FATAL]'). ' [' . $time . ']: '.get_class($e)
                        ."\n".'========================================================='
                        ."\n".'MESSAGE: ' .substr($e->getMessage(), 0, 4000)
                        ."\n".'------------------------------------------------------------------------------------------------------------------'
                        ."\n".'URL: '.$url
                        ."\n".'IP: '.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '')
                        ."\n".'FILE: '.$e->getFile() . ' ('.$e->getLine().')'
                        ."\n".'TRACE: '.$stack_trace
                        ."\n",
                    FILE_APPEND
                );
        }catch(\Exception $e){
            if($is_fatal){
                die('<span style="color: red;">Could not write to error log!</span>');
            }
        }
    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new self();
    }

    /**
     * Get THE singleton of class instance or create it if not exists
     */
    private static $instance;
    public static function get(){
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
        return new errorHandler();
    }
}
