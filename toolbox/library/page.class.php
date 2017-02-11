<?php
namespace toolbox;
/**
 * Template helper for rendering ALL output to the user (templates/views/etc.).
 */
class page {

    private $views = array();
	private $header_contents = null;
	private $exit_on_main = false;

	function __construct($exit_on_main = false){
		$this->exit_on_main = $exit_on_main;
	}

	function setExitOnMain($exit_on_main){
		$this->exit_on_main = $exit_on_main;
        return $this;
        return new page();
	}

	private $http_response_code;
	function setHttpResponseCode($code){
		$this->http_response_code = $code;
		return $this;
        return new page();
	}

	/**
	 * SET HTTP CONTENT HEADER OF CURRENT PAGE
	 */
    public function setHeader($header_content){
        $this->header_contents = $header_content;
        return $this;
        return new page();
    }

	/**
	 * GET HTTP CONTENT HEADER OF CURRENT PAGE
	 */
    public function getHeader(){
        return $this->header_contents;
    }

    public function set($variable_name, $variable_value){
    	$this->{$variable_name} = $variable_value;
        return $this;
        return new page();
    }

    public function getVar($variable_name){
    	return $this->{$variable_name};
    }

	/**
	 * Add a view to be rendered.
	 */
    public function addView(
    	$view_path,
    	$group = 'automatically-rendered',
    	$position = null
	){
		if(is_string($view_path)){
			$view_path = ltrim($view_path, '/');
		}
    	if($position === null){
        	$this->views[$group][] = $view_path;
    	}else{
    		$this->views[$group][$position] = $view_path;
    	}
        return $this;
        return new page();
    }

    function setMainView($view_path){
        $this->views['automatically-rendered'][0] = $view_path;
		return $this;
		return new page();
    }

    private $no_clear = array();
    function setNoClear($view_group){
        $this->no_clear[$view_group] = $view_group;

        return $this;
        return new page();
    }
    public function clearViews($group = null){
    	if($group === null){
            foreach(array_keys($this->views) as $group){
                if(in_array($group, $this->no_clear)){
                    continue;
                }
                unset($this->views[$group]);
            }

    		//$this->views = array();
            return $this;
            return new page();
    	}
    	if(!isset($this->views[$group])){

    	}else{
    		$this->views[$group] = array();
		}

		return $this;
		return new page();
    }


    public function getViews($group){
    	if(!isset($this->views[$group])){
    		return array();
    	}

    	return $this->views[$group];
    }

    public function getNextView($group, $reset = false){
    	if(!isset($this->views[$group])){
    		return false;
    	}
		if($reset){
			$current = current($this->views[$group]);
			next($this->views[$group]);
			return $current;
		}
        return array_shift($this->views[$group]);
    }

	private $current_view_group = null;
	function renderViews($group = 'automatically-rendered', $reset = false){
		while($view = $this->getNextView($group, $reset)){
			$this->current_view_group = $group;
			$this->render($view);
		}
		if($reset && isset($this->views[$group])){
			reset($this->views[$group]);
		}
		if($group === 'automatically-rendered' && $this->exit_on_main === true){
            utils::vdd('test');
			exit;
		}
		return $this;
		return new page();
	}

	function countViews($group = 'automatically-rendered'){
	    if(!isset($this->views[$group])){
	        return 0;
	    }
		return count($this->views[$group]);
	}

    private $cache_seconds = false;
    function setCache($seconds_to_cache){
        $this->cache_seconds = $seconds_to_cache;
        return $this;
        return new page();
    }

    public $page_modified_time = false;
    function setPageModifiedTime($page_modified_time){
        $this->page_modified_time = $page_modified_time;
        return $this;
        return new page();
    }


	function render($view){
		if(!headers_sent()){
			if($this->header_contents !== null){
				header($this->header_contents);
			}elseif(isset($this->http_response_code)){
    		    if(function_exists('http_response_code')){
    			    http_response_code($this->http_response_code);
    			}else{
    				utils::http_response_code($this->http_response_code);
    			}
            }

            if($this->cache_seconds !== false){
                $ts = gmdate("D, d M Y H:i:s", time() + $this->cache_seconds) . " UTC";
                header("Expires: $ts");
                header("Pragma: cache");
                header("Cache-Control: max-age=".$this->cache_seconds);
            }

            if($this->page_modified_time !== false){
                $last_modified_time = $this->page_modified_time;
                $etag = md5($last_modified_time);
                // always send headers
                header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
                header("Etag: $etag");
                // exit if not modified
                if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
                    @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                    header("HTTP/1.1 304 Not Modified");
                    exit;
                }
            }

		}

		if(is_object($view) && get_class($view) !== 'Closure'){
			$view->renderViews();
		}elseif(is_callable($view)){
			call_user_func($view, $this);
		}else{
			extract(get_object_vars($this), EXTR_REFS);
			require toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/templates/'.$view;
		}

	}

	public function setArray($array_name, $item, $value = null){
	    if(empty($item)){
	        return $this;
	    }
        if($value !== null){
            $this->{$array_name}[$item] = $value;
        }else{
            $this->{$array_name}[] = $item;
        }
		return $this;
        return new page();
	}

	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create($name = null){
		return new self(false);
        return new page();
	}


	/**
	 * Get THE singleton of class instance
	 * (creates it if not exists)
	 */
	private static $instances;
	public static function get($name = null){
		if($name === null){
			$name = 'singleton';
		}

		if (!self::instanceExists($name)) {
			self::$instances[$name] = new self();
		}

		return self::$instances[$name];
		return new page();
	}


	public static function instanceExists($name){
		if(isset(self::$instances[$name])){
			return true;
		}

		return false;
	}


}