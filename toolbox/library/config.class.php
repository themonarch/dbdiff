<?php
namespace toolbox;
class config {


    private $config;
    function __construct(){
        require_once toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/config.php';
    }

    function getConfig($name, $item = null){

        if($item !== null){
            return $this->config[$name][$item];
        }

        return $this->config[$name];

    }

    function setConfig($name, $value){
        $this->config[$name] = $value;
    }

    function isSetConfig($name, $item = null){
        if(!isset($this->config[$name])){
            return false;
        }

        if($item !== null && !isset($this->config[$name][$item])){
            return false;
        }

        return true;

    }

    static function hasSetting($name, $item = null){
        return config::get()->isSetConfig($name, $item);
    }

    static function getSetting($name, $item = null){
        return config::get()->getConfig($name, $item);
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
        return new self();
        return new config();
    }
}
