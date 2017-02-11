<?php
namespace toolbox;
class router {

    /**
     * The string to append when looking for file of a controller
     */
    protected $file_append = null;

    /**
     * The original requested uri as a string.
     */
    protected $uri_string = '';

    /**
     * The original requested uri as an array.
     */
    protected $uri_array = '';

    /**
     * The remaining uri that has not been resolved
     */
    protected $uri_remainder = '';

    /**
     * The path to the controller we are resolving to.
     */
    protected $resolvedPath = '';

    /**
     * If redirecting mutiple times, this stores path from previous resolve.
     */
    protected $lastResolvedPath = '';
    /**
     * The file name of the controller we are resolving to.
     */
    protected $resolvedFile = '';

    /**
     * The class name of the controller that was resolved.
     */
    protected $controllerClassName = null;

    /**
     * If config for treatQueryAsUrl is false, set to the cut string.
     */
    protected $uri_cut_query_string = null;

    /**
     * The class name of the controller that was resolved.
     */
    protected $get = array();

    function toInternal($uri, $exit = true){
        $this->to($uri, 'internal', '_internal');
        if($exit === true){
        	exit;
		}
    }

    function to($uri, $initial_path = 'controllers', $file_append = '_controller', $exit = true){
        require_once toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/hooks/beforeRouter.php';
        require toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/hooks/beforeRouterMultiple.php';


        $this->lastResolvedPath = $this->resolvedPath;
        $this->file_append = $file_append;
        $config = config::get();
        if($config->getConfig('uri_path') !== false){
            $uri = utils::removeStringFromBeginning($uri, $config->getConfig('uri_path'));
        }

        //reset from last controller
        $this->resolvedPath = toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/'.$initial_path;

        //strip any slashes from beginning
        $this->uri_string = ltrim(trim($uri), '/');

        $length = strpos($this->uri_string, '?');
        if($length === false)
            $length = strlen($this->uri_string);

        //remove any query string (ei: ?foo=bar)
        if($config->getConfig('treatQueryAsUrl') !== true){
            $this->uri_cut_query_string = (string)substr($this->uri_string, $length);
            $this->uri_string = (string)substr($this->uri_string, 0, $length);
        }

        //split the array into pieces so we can look for associated controllers
        $this->uri_array = explode('/', utils::removeStringFromEnd($this->uri_string, '/'));

        //make sure we always start with index
        if($this->uri_array[0] != '')
            array_unshift($this->uri_array, '');

        $this->uri_remainder = $this->uri_array;

        if(//if controller was not found or invalid
            !$this->resolveController()
        ){
            router::get()->toInternal('404');
            return;
        }

        if(method_exists($this->controllerClassName, 'setup')){
            call_user_func($this->controllerClassName.'::setup');
        }

        //validate access control permissions
        if(false === $this->validateParameters()){
            return;
        }

        //validate access control permissions
        if(false === accessControl::get()->validatePermissions()){
            return;
        }

        require_once toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/hooks/beforeControllerExecute.php';


        //instantiate the controller
        $controller = new $this->controllerClassName;

        require_once toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/hooks/afterControllerExecute.php';

        if($exit){
            exit;
        }

    }



    /**
     * Takes a uri and maps to an existing controller
     */
    protected function resolveController(){
        //look for existing folders and pages
        do{

            //folder and file exists, append this path to our resolved path if not blank
            if(trim($this->uri_remainder[0]) != '')
                $this->resolvedPath .= '/'.$this->uri_remainder[0];
            //get the expected file name
            $filename = $this->uri2filename($this->uri_remainder[0]);

            //cut this path from the uri remainder so we dont try to resolve it again
            $this->uri_remainder = array_slice($this->uri_remainder, 1);

            //if page file exists in the path, require it
            if(file_exists($this->resolvedPath.'/'.$filename.'.php')){
                $this->resolvedFile = $filename;
                require_once $this->resolvedPath.'/'.$filename.'.php';

                //get the expected class name
                $classname = $this->filename2classname($filename);
                //if the class name exists
                if(class_exists($classname)){
                    $this->controllerClassName = $classname;

                    //if a passThru method exists in the class
                    if(method_exists($classname, 'passThru')){
                        //call the passThru with the remaining uri
                        $classname::passThru($this->uri_remainder);
                    }
                }else{
                    $this->controllerClassName = false;
                }
            }

        }while(
            isset($this->uri_remainder[0])
            && $this->uri_remainder[0] !== ''//prevents trailing slashes
            && file_exists($this->resolvedPath.'/'.$this->uri_remainder[0])
        );

        //look for existing page inside the folder
        if(
            $this->controllerClassName == false
            || !method_exists($this->controllerClassName, '__construct')
        ){
            return false;
        }

        return true;
    }

    private $maxParams = 0, $minParams = 0;
    /**
     * If number of parameters exceeds expected numbers,
     * controller is not correct for uri.
     */
    protected function validateParameters(){
        if(
            (
                $this->maxParams !== false
                && $this->maxParams < count($this->uri_remainder)
            ) || (
                $this->minParams !== false
                && $this->minParams > count($this->uri_remainder)
            )
        ){
            router::get()->toInternal('404');
            return false;
        }
    }

    /**
     * The maximum number of parameters uri remainders the page can
     * have before we show a 404 page
     * false = unlimited
     */
    function setMaxParams($int_or_bool){
        $this->maxParams = $int_or_bool;
        return $this;
        return new router();
    }

    /**
     * The minimum number of parameters uri remainders the page can
     * have before we show a 404 page
     * false = unlimited
     */
    function setMinParams($int_or_bool){
        $this->minParams = $int_or_bool;
        if($int_or_bool > $this->maxParams){
            $this->setMaxParams($int_or_bool);
        }
        return $this;
        return new router();
    }

    protected function uri2filename($path){
        //set default name to index
        if(trim($path) == '') $path = 'index';

        return $path;
    }

    protected function filename2classname($filename){
        $classname = str_replace(array('/', '-', "."), '_', $filename).$this->file_append;
        if(is_numeric(substr($classname, 0, 1))){
            $classname = '_'.$classname;
        };

        return 'toolbox\\'.$classname;
    }





    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new router();
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
        return new router();
    }


    public function getUri(){
        return $this->uri_string;
    }

    public function getUriRemainder(){
        return $this->uri_remainder;
    }

    public function getUriCut(){
        return $this->uri_cut_query_string;
    }

    public function getResolvedPath(){
        return $this->resolvedPath;
    }
    public function getResolvedPathPrevious(){
        return $this->lastResolvedPath;
    }

    public function getResolvedFile(){
        return $this->resolvedFile.'.php';
    }

    /**
     * add a piece (folder path) to the remaing uri
     */
    public function addToUriRemainder($part, $pos = 0){
        return array_splice( $this->uri_remainder, $pos, 0, $part );
    }

    /**
     * Save piece of REMAINING uri at $pos into the $_GET array under the given $key (slices it out of uri).
     * If no $key given, appends to the next availabe key in array.
     * If no $pos given, uses the first part of remaining uri.
     * If all is set to true, the full remaining uri including slashes will be extracted
     */
    public function extractParam($key = null, $pos = 0, $all = false){

        $param = $this->paramPeek($pos, $all);
        if($param === false){
            return false;
        }

        if($key === null){
            array_push($this->get, $param);
        }else{
            $this->get[$key] = $param;
        }

        $this->removeParam($pos, $all);

        return true;
    }

    /**
     * Save LAST piece of REMAINING uri at $pos into an array under the given $key (slices it out of uri).
     * If no $key given, appends to the next availabe key in array.
     * returns true of success, false if no param extracted.
     */
    public function extractParamLast($key = null){
        $pos = count($this->getUriRemainder())-1;
        $param = $this->paramPeek($pos, false);
        if($param === false){
            return false;
        }
        if($key === null){
            array_push($this->get, $param);
        }else{
            $this->get[$key] = $param;
        }

        $this->removeParam($pos, false);

        return true;
    }

    /**
     * See what the next param is without modifying.
     */
    public function paramPeek($pos = 0, $all = false){

        if(!$this->isAllowedToExtractParams()){
            return false;
        }
        $remaining_uri = $this->getUriRemainder();

        if(!isset($remaining_uri[$pos]))
            return false;
        if($all){
            $param = urldecode(implode('/', array_slice($remaining_uri, $pos)));
        }else{
            $param = urldecode($remaining_uri[$pos]);
        }

        return $param;

    }

    private $allowedToExtractParams = true;
    /**
     *
     */
    public function isAllowedToExtractParams(){
        if($this->allowedToExtractParams === null || $this->allowedToExtractParams === false){
            return false;
        }
        return true;
    }

    /**
     * returns get param extracted from uri if exists, or false if not exists.
     * if no key passed, returns entire param array
     */
    public function getParam($key = null){
        if($key === null){
            return $this->get;
        }
        if(isset($this->get[$key])){
            return $this->get[$key];
        }

        return false;
    }

    public function setAllowedToExtractParams($boolean){
        if(!is_bool($boolean))
           $boolean = false;
        $this->allowedToExtractParams = $boolean;
    }


    /**
     *
     */
    public function removeParam($pos, $all = false){
        if($all){
            $this->uri_remainder = array();
        }else{
            unset($this->uri_remainder[$pos]);
            $this->uri_remainder = array_values($this->uri_remainder);
        }

    }



}
