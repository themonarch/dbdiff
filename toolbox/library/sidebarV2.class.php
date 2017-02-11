<?php
namespace toolbox;
class sidebarV2 extends page {
	function __construct(){
        parent::__construct();
		$this->setMainView('sidebar/sidebar.php');
		$this->setClass('');
		$this->setAttributes('');
		$this->active = array();
	}

	function addLink(page $link){
		$this->setArray('links', $link);
		return $this;
		return new sidebarV2();
	}

    function getLink($link_id){
        foreach ($this->links as $link) {
            if($link->getID() === $link_id){
                return $link;
            }
        }

        return null;
        return new sidebarLink();
    }

	function setClass($class){
		$this->set('class', $class);
		return $this;
		return new sidebarV2();
	}

	private $active;
	function setActive($link_id){
		$this->active = func_get_args();

        $this->applyActive($this->active, $this->links);

		return $this;
		return new sidebarV2();
	}

    private function applyActive($active_array, $links){
        $item = array_shift($active_array);
        foreach ($links as $link) {
            if($link->getID() === $item){
                $link->set('active', true);
                if(isset($link->links) && !empty($active_array)){
                    $this->applyActive($active_array, $link->links);
                }
            }else{
                $link->set('active', false);
            }
        }
    }

	function getCurrentActive(){
		return current($this->active);
	}

	function getNextActive(){
		array_pop($this->active);
		return $this->active;
	}

	function setAttributes($var){
		return $this->set('custom_attributes', $var);
		return new sidebarLink();
	}

	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create($name = null){
		return new self(false);
        return new sidebarV2();
	}

	static function createLink($id){
		return sidebarLink::create($id);
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
			self::$instances[$name] = new self($name);
		}

		return self::$instances[$name];
		return new sidebarV2();
	}


	public static function instanceExists($name){
		if(isset(self::$instances[$name])){
			return true;
		}

		return false;
	}

	public static function destroyInstance($name){
		if(isset(self::$instances[$name])){
			unset(self::$instances[$name]);
		}
	}

    public static function countInstances(){
        return count(self::$instances);
    }
}

class sidebarLink extends page {
	function __construct($id){
        parent::__construct();
		$this->setMainView('sidebar/link.php');
		$this->set('id', $id);
		$this->set('inner', $id);
        $this->setContainerAttributes('');
        $this->setContainerClass('');
        $this->setLinkAttributes('');
	}

	function getID(){
		return $this->id;
	}

	function setHref($href){
		return $this->set('href', $href);
		return new sidebarLink();
	}

	function setLinkAttributes($var){
		return $this->set('link_attributes', $var);
		return new sidebarLink();
	}
	function setContainerAttributes($var){
		return $this->set('container_attributes', $var);
		return new sidebarLink();
	}

	function setContainerClass($var){
		return $this->set('container_class', $var);
		return new sidebarLink();
	}

	function setInner($inner){
		return $this->set('inner', $inner);
		return new sidebarLink();
	}

	function setClass($class){
		return $this->set('class', $class);
		return new sidebarLink();
	}

	public $active = false;
	public $sub_active = array();
	function setActive($sub_active){
		$this->active = true;
		$this->sub_active = $sub_active;
		return $this;
		return new sidebarLink();
	}

	function getCurrentActive(){
		return current($this->sub_active);
	}

	function getNextActive(){
		array_pop($this->sub_active);
		return $this->sub_active;
	}

	function addLink(page $link){
		$this->setArray('links', $link);
		return $this;
		return new sidebarLink();
	}

	/**
	 * Creation factory
	 * (creates NEW instance each time)
	 */
	static function create($id = null){
		return new self($id);
        return new sidebarLink();
	}


}
