<?php
namespace toolbox;

class toolbox{
    private $app_folder;

    /**
     * Start the app corresponding to the name of the app folder.
     */
    final function __construct($app_folder){

        $this->app_folder = $app_folder;
    }

    private $limit_autoload = true;
    /**
     * By default ($bool = true), the toolbox only tries to autoload classes
     * that are part of the toolbox namespace OR app name's namespace.
     * Set to false to autoload ANY classes in ANY namespace (may decrease performance)
     */
    function setLimitedAutoLoading($bool){
       $this->limit_autoload = $bool;
       return $this;
       return new toolbox();
    }

    function getLimitAutoLoad(){
       return $this->limit_autoload;
    }

    private $enable_routing = true;
    /**
     * By default ($bool = true), the toolbox will attempt to
     * route to controllers based on url. Set to false if not
     * using dynamic urls
     */
    function setRouting($bool){
       $this->enable_routing = $bool;
       return $this;
       return new toolbox();
    }

    private $url_path = '';
    /**
     * Set the path to the app if not installed at document root.
     * This value will be stripped from the url when mapping to
     * a controller. (MUST START WITH FORWARD SLASH)
     */
    function setUrlPath($string){
       $this->url_path = $string;
       return $this;
       return new toolbox();
    }

    function getUrlPath(){
       return $this->url_path;
    }

    private $handle_errors = true;
    function setErrorHandling($boolean){
        if($boolean){
            $this->handle_errors = true;
        }else{
            $this->handle_errors = false;
        }

        return $this;
        return new toolbox();
    }

    function getErrorHandling(){
        return $this->handle_errors;
    }

    static $error_warning = false;
    private $started = false;
    /**
     * Start the framework
     */
    function start(){

        if($this->started === true){//don't start if already started!
            throw new toolboxException("Framework already started!", 1);
        }

        $this->started == true;

        // Set path to be relative to this toolbox folder
        self::$path = __DIR__;

        require_once toolbox::getPath().'/'.$this->app_folder.'/hooks/beforeToolbox.php';

        //setup autoloading of framework classes + project specific classes
        spl_autoload_register(function($class){
            $class_explode = explode('\\', $class);
            $app_folder = toolbox::get()->getAppFolderName();

            if(//don't include files from classes not part of our namespaces
               toolbox::get()->getLimitAutoLoad()//if restricted to certain namespaces
                && (
                     isset($class_explode[0])//there is namespace
                     && (
                      $class_explode[0] !== 'toolbox'//namespace isn't part of toolbox library
                      && $class_explode[0] !== $app_folder//AND namespace isn't part of the app
                     )
                 )
             ){
                return;//don't try to include the file
            }

            if(//file is part of the framework
                $class_explode[0] === 'toolbox'
                && file_exists(toolbox::getPath().'/library/' . end($class_explode) . '.class.php')
                && include toolbox::getPath().'/library/' . end($class_explode) . '.class.php'
            ){

            //if file is part of the app
            }elseif(file_exists(toolbox::getPath().'/'.$app_folder.'/classes/' .  end($class_explode) . '.class.php')){
                require(toolbox::getPath().'/'.$app_folder.'/classes/' .  end($class_explode) . '.class.php');
            }elseif(//look again in the framework (this allows app classes to override framework classes)
                file_exists(toolbox::getPath().'/library/' . end($class_explode) . '.class.php')
                && include toolbox::getPath().'/library/' . end($class_explode) . '.class.php'
            ){}else{//file wasn't found
                return;
            }
        });

		//when script execution ends, this function will process any unhandled errors.
        register_shutdown_function(array(&$this, "shutdown"));

        //additional error handling
        if($this->handle_errors){

            //catch strict errors & notices and log and/or display them
            set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext){
				//if we already have a warning
                if(toolbox::$error_warning !== false){
                    return false;//don't replace it
                }

                $warnings = 'fatal';
                if(config::hasSetting('errors', 'warnings')){
                    $warnings = config::getSetting('errors', 'warnings');
                }

                if(//if treating as fatal error, throw and exit
                    $warnings === 'fatal'
                ){
                    throw new toolboxError($errstr, $errno);
				}elseif($warnings === 'off'){
                    return false;
                }else{//store it for the shutdown function to handle it at end of execution
                    toolbox::$error_warning = array(
                    	'errno' => $errno,
                    	'errstr' => $errstr,
                    	'errfile' => $errfile,
                    	'errline' => $errline,
                    	'errcontext' => $errcontext
					);
				}

                return false;//return false to pass the error along to default PHP error handler

            }, (E_ALL | E_STRICT) /*& ~E_WARNING*/);

			//default exception handling
            set_exception_handler('\toolbox\errorHandler::exceptionHandler');

        }

        $config = config::get();
        $error_handler = errorHandler::get();

        //configure error notifications
        if($config->isSetConfig('errors', 'emails')){
            foreach($config->getConfig('errors', 'emails') as $key => $email){
                $notify_fatal_errors = true;
                $notify_nonfatal_errors = true;
                if(is_array($email)){
                    $notify_fatal_errors = $email['fatal'];
                    $notify_nonfatal_errors = $email['nonfatal'];
                    $email = $key;
                }

                $error_handler->addEmail($email, $notify_fatal_errors, $notify_nonfatal_errors);
            }
        }

        if($config->isSetConfig('errors', 'log_file') && $config->getConfig('errors', 'log_file') === false){
            $error_handler->setErrorLogFile(false);
        }



        require_once toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/hooks/afterToolbox.php';

        //if routing enabled
        if($this->enable_routing){
            $current_url = utils::removeStringFromBeginning($_SERVER['REQUEST_URI'], $this->url_path);
            //start the router to find a controller 'page' for the current url
            return router::get()->to($current_url, 'controllers', '_controller', false);
        }

    }

    function getAppFolderName(){
        return $this->app_folder;
    }

    private static $path;
    /**
     * path to toolbox folder
     */
    static function getPath(){
        return self::$path;
    }

    /**
     * Path to app folder (/toolbox/example_app/)
     */
    static function getPathApp(){
        return self::getPath().'/'.toolbox::get()->getAppFolderName();
    }

    private static $instance = null;
    /**
     * Get the instance of THE app or create if not exists.
     */
    public static function get($app_folder = 'example_app'){
        if(!isset(self::$instance)){
            self::$instance = new self($app_folder);
        }
        return self::$instance;
        return new toolbox();
    }

    static $error_handled = false;
    function shutdown(){
        //check for error set by our error handler
        if(toolbox::$error_warning !== false){
            try{//log the error as non-fatal
                throw new toolboxError(toolbox::$error_warning['errstr']
                    . "\nfile: ".toolbox::$error_warning['errfile'].' ('.toolbox::$error_warning['errline'].')'
                    . "\nContext: ".utils::array2string(toolbox::$error_warning['errcontext']),
                toolbox::$error_warning['errno']);
            }catch(toolboxError $e){
                errorHandler::logNonFatalError($e);//non-fatal error, log and continue
			}
        }


        $error = error_get_last();

        if(//if...
            $error !== null//there is an error
            && !toolbox::$error_handled//and our framework didn't handle it already
            && toolbox::get()->getErrorHandling()//and error handling enabled
        ){
            try{
                throw new toolboxException($error['message']
                    . ' in file '.$error['file'].' on line '.$error['line'], $error['type']);
            }catch(toolboxException $e){
                errorHandler::passError($e);
            }
        }

    }

}

class toolboxException extends \Exception {}//unexpected errors
class toolboxError extends toolboxException {}//notices, warnings, strict, etc