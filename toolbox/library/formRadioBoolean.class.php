<?php
namespace toolbox;

class formRadioBoolean extends page {

    function __construct(){
        $this->setMainView('/widgets/form/formRadioBoolean.php');
    }

    /**
     * Set post values from cookies + error messages
     */
    private function readValues(){
        $this->error = messages::readMessages('wrong', $this->name);
        if($this->error !== null){
            $this->wrapper_class = ' error';
        }

        $value = messages::readMessages('form', $this->name);
        if($value !== null){
            $this->value = htmlspecialchars($value);
        }
    }

	function renderViews($group = 'automatically-rendered'){
		$this->readValues();
		parent::renderViews($group);
	}

    private $data;
    function setLabel($value){
        $this->set('label', $value);
		if(!isset($this->name)){
			$this->setName(utils::toSlug($value));
		}
        return $this;
        return new formRadioBoolean();
    }

    function setName($value){
        $this->set('name', $value);
        return $this;
        return new formRadioBoolean();
    }

    function setValue($value){
        $checked = false;
        if(
            $value === '1'
            || $value === true
            || $value === 'true'
            || $value === 'yes'
            || $value === 'on'
            || $value === 1
        ){
            $checked = true;
        }
        $this->set('value', $checked);
        return $this;
        return new formRadioBoolean();
    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create($name = 'singleton'){
        self::$instances[$name] = new self();
        return new formRadioBoolean();
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
        return new formRadioBoolean();
    }
}

