<?php
namespace toolbox;
class accessControl {

    private $required = array();
    private $callbacks = array();
    /**
     * Set a required permission that MUST be granted
     * to the user before the controller is executed
     * @param $callback function optional anonymous callback
     * to call when the requirement is NOT met.
     */
    function requires($requirement, $callback = null){
        $this->required[$requirement] = true;
        $this->callbacks[$requirement] = $callback;
        return $this;
        return $this;
        return new accessControl();
    }

    private $granted = array();
    function grant($grant){
        $this->granted[$grant] = true;
        return $this;
        return new accessControl();
    }

    function deny($grant){
        $this->granted[$grant] = false;
		return $this;
        return $this;
        return new accessControl();
    }

    function clearRequirements(){
        $this->required = array();
        return $this;
        return new accessControl();
    }

    function removeRequired($requirement){
        $this->required[$requirement] = false;
        $this->callbacks[$requirement] = false;
        return $this;
        return new accessControl();
    }

    /**
     * check if a specific grant is currently needed
     */
    function isRequired($requirement){
        return (isset($this->required[$requirement]) && $this->required[$requirement] !== false);
    }

    private $super_user = false;
    /**
     * Enable access to ALL privileges
     */
    function setSuperUser($bool){
        if($bool){
            $this->super_user = true;
        }else{
            $this->super_user = false;
        }
    }

    function validatePermissions(){
        foreach($this->required as $key => $value){
            if($value !== true){
                continue;
            }

            if($this->hasRequirement($key)){
                continue;
            }

            if(isset($this->callbacks[$key])){
                call_user_func($this->callbacks[$key], $key);
            }

            router::get()->toInternal('access_denied');
            return false;

        }
    }
    function hasRequirement($requirement){
        if(isset($this->granted[$requirement])){

            if($this->granted[$requirement] === true){
                return true;
            }else{
                return false;
            }
        }

        if($this->super_user){
            return true;
        }

        return false;
    }
    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new self();
        return new accessControl();
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
        return $this;
        return new accessControl();
    }
}
