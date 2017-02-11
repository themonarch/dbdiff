<?php
namespace toolbox;
class title extends page {
    function __construct(){
        $this->setMainView('elements/title/medium.php');
}


    private $separator = ' &laquo; ';
    function setSeparator($string){
        $this->separator = $string;
    }

    private $pieces = array();
    function addCrumb($name, $link = false){
        $key = count($this->pieces);
        $this->pieces[$key]['title'] = $name;
        $this->pieces[$key]['html_striped_title'] = strip_tags($name);
        $this->pieces[$key]['href'] = $link;

        return $this;
        return new title();
    }


    private $subtitle = false;
    /**
     * String override for subtitle. False = default.
     */
    function setSubtitle($string){
        $this->subtitle = $string;

        return $this;
        return new title();
    }

    function setSubtitleDisabled(){
        $this->subtitle = null;
        return $this;
        return new title();
    }

    function hasSubtitle(){
        return ($this->subtitle !== null);
    }

    function getSubtitle(){
        if(trim($this->subtitle) !== ''){
            return $this->subtitle;
        }

        return $this->getTitleString();

    }

    function renderViews($group = 'automatically-rendered', $reset = false){
        if($this->subtitle === null){
            return;
        }
        parent::renderViews($group, $reset);

    }

    function getTitleString(){
        $title = '';
        foreach (array_reverse($this->pieces) as $arr) {
            $title .= $arr['html_striped_title'].$this->separator;
        }

        return utils::removeStringFromEnd($title, $this->separator);
    }

    private static $instances = array();
    public static function get($name = 'singleton'){
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self();
        }
        return self::$instances[$name];
        return new title();
    }

}
