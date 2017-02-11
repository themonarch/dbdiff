<?php
namespace toolbox;
class menu {
    private $menu_view = 'menu/default.php';
    private $item_view = 'menu/item_default.php';

    /**
     * Set the default view (path to file or anonymous function)
     * to use for rendering menu.
     */
    function setMenuDefaultView($view){
        $this->menu_view = $view;
        return $this;
        return new menu();
    }

    /**
     * Set the default view (path to file or anonymous function)
     * to use for rendering items.
     */
    function setItemDefaultView($view){
        $this->item_view = $view;
        return $this;
        return new menu();
    }

    private $items = array();
    private $current_item = null;
    function addItem($item_name){
        $this->current_item = $item_name;
        if(!isset($this->items[$item_name])){
            $this->items[$item_name] = array('item_name' => $item_name);
            $this->items[$this->current_item]['display_name'] = $item_name;
            $this->items[$this->current_item]['disabled'] = false;
        }

        return $this;
        return new menu();
    }

	function countItems(){
		return count($this->items);
	}

    function setHref($href){
        $this->items[$this->current_item]['href'] = $href;
        return $this;
        return new menu();
    }

    function setClass($value){
        $this->items[$this->current_item]['class'] = $value;
        return $this;
        return new menu();
    }

    function setAttr($value){
        $this->items[$this->current_item]['attr'] = $value;
        return $this;
        return new menu();
    }

    function setItemView($view){
        $this->items[$this->current_item]['view'] = $view;
        return $this;
        return new menu();
    }

    function setDisabled($boolean = true){
        $this->items[$this->current_item]['disabled'] = $boolean;
        return $this;
        return new menu();
    }

    function setCustomClass($class){
        $this->items[$this->current_item]['class'] = $class;
        return $this;
        return new menu();
    }

    /**
     * Contents to append after item element.
     *
     * @param $view anonymous function or path to view
     */
    function addViewDropdown($view){
        $this->items[$this->current_item]['dropdown'] = $view;
        return $this;
        return new menu();
    }

    function setDisplayName($name){
        $this->items[$this->current_item]['display_name'] = $name;
        return $this;
        return new menu();
    }

    function setMeta($meta_name, $value){
        $this->items[$this->current_item][$meta_name] = $value;
        return $this;
        return new menu();
    }

    private $active = null;
    function setActive($bool_or_id = true){
        if($bool_or_id === true){
            $this->active = $this->current_item;
        }elseif($bool_or_id === false){
            $this->active = null;
        }else{
            $this->active = $bool_or_id;
        }

        return $this;
        return new menu();
    }

    function render(){
        require toolbox::getPathApp().'/templates/'.$this->menu_view;
    }


    function getNextItem($reset = false, $key_only = true){
        $item = key($this->items);
        if($item === null || $reset){
            reset($this->items);
            return false;
        }else{
            next($this->items);
            if($key_only){
                return $item;
            }else{
                return $this->items[$item];
            }
        }

    }

    function getMeta($meta_name, $item_name){
        if(!isset($this->items[$item_name][$meta_name])){
            return null;
        }
        return $this->items[$item_name][$meta_name];
    }

    function getItem($item_name){
        return $this->items[$item_name];
    }

    function getActive(){
        return $this->active;
    }

    private function renderItems(){
        foreach($this->items as $key => $item){
            if(isset($item['view'])){
                if(is_callable($item['view'])){
                    call_user_func($item['view'], $this);
                }else{
                    require toolbox::getPath().'/'.toolbox::get()
                                ->getAppFolderName().'/templates/'.$item['view'];
                }
            }else{
                if(is_callable($this->item_view)){
                    call_user_func($this->item_view, $this);
                }else{
                    require toolbox::getPath().'/'.toolbox::get()
                                ->getAppFolderName().'/templates/'.$this->item_view;
                }
            }
        }
    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = 'singleton'){
        self::$instances[$name] = new self();
        return self::$instances[$name];
        return new menu();
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
        return new menu();
    }
}
