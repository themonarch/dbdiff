<?php
namespace toolbox;
class sidebar{
	private $links = array();
	private $sub_links = array();
	private $active = null;
	private $sub_active = null;
	private $href = null;
	private $text_left = '';
	private $text_inner_left = '';
	function __construct(){

	}

	private $current;
	private $duplicate_count = 0;
	function addLink(
		$name,
		$subtitle = null,
		$href = null,
		$template = 'default.php',
		array $custom_data = array()
	){
		if(isset($this->links[$name])){
			while(isset($this->links[$name.'_'.++$this->duplicate_count])){

			}
			$name = $name.'_'.$this->duplicate_count;
		}


		$this->duplicate_count = 0;

		$this->current = $name;
		//utils::vd(debug_backtrace());

		unset($this->links[$name]);
		$this->links[$name]['href'] = $href;
		$this->links[$name]['template'] = $template;
		$this->links[$name]['subtitle'] = $subtitle;
		$this->links[$name]['custom_data'] = $custom_data;
		$this->links[$name]['custom_data'] = $custom_data;
        return $this;
        return new sidebar();
	}

	private $running = false;
	function loop(){
		if($this->running === false){
			reset($this->links);
			$this->running = true;
		}else{
			next($this->links);
		}

		if(!current($this->links)){
			$this->running = false;
		}

		return key($this->links);

	}

	function addCustomData($key, $value){
		$this->links[$this->current]['custom_data'][$key] = $value;
        return $this;
        return new sidebar();
	}

	function setLink($name){
		$this->current = $name;
        return $this;
        return new sidebar();

	}

	function addSubLink(
		$parent,
		$name,
		$href = '#',
		$template = 'default.php',
		array $custom_data = array()
	){
		//utils::vd(debug_backtrace());
		unset($this->sub_links[$parent][$name]);
		$this->sub_links[$parent][$name]['href'] = $href;
		$this->sub_links[$parent][$name]['template'] = $template;
		$this->sub_links[$parent][$name]['custom_data'] = $custom_data;
		return $this;
        return new sidebar();
	}

	private $class = '';
	function setClass($class){
		$this->class = $class;
	}

	function moveLinkToTop($name){
		$link = $this->links[$name];
		unset($this->links[$name]);
		$this->links = array_merge(array($name => $link), $this->links);
        return $this;
        return new sidebar();
	}

	function setLeftText($text){
		$this->text_left = $text;
        return $this;
        return new sidebar();
	}
	function setLeftInnerText($text){
		$this->text_inner_left = $text;
        return $this;
        return new sidebar();
	}

	function setSubActive($parent, $name){
		$set = false;
		if(!empty($this->sub_links[$parent]))
		foreach($this->sub_links[$parent] as $key=>$value){
			if($key === $name){
				$this->sub_active = $key;
				$set = true;
			}
		}
        //thow exception if not set??

        return $this;
        return new sidebar();
	}

	function setActive($name){
		$set = false;
		foreach($this->links as $key=>$value){
			if($key === $name){
				$this->active = $key;
                $this->sub_active = null;
				$set = true;
			}
		}
        //thow exception if not set??

        return $this;
        return new sidebar();
	}

	function setTitle($title, $href=""){
		$this->title = $title;
		$this->href = $href;

        return $this;
        return new sidebar();
	}

	function render($template = 'sidebar.php'){
		require toolbox::getPathApp().'/templates/modules/sidebar/'.$template;
	}


	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create(){
		return new self();
	}

    /**
     * Get THE singleton of class instance
     * (creates it if not exists)
     */
    private static $instances = array();
    public static function get($name = 'singleton'){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self();
        }
        return self::$instances[$name];
        return new self();
        return new sidebar();
    }


    public static function countInstances(){
        return count(self::$instances);
    }

}
