<?php
namespace toolbox;

class formV2Radio extends formV2 {

    function __construct(){
        parent::__construct();
        $this->setMainView('/formV2/radio.php');
        $this->setChecked(false);
        $this->setStyle('style2');
    }

    function setChecked($boolean){
        $this->set('checked', $boolean);

        return $this;
        return formV2Radio();
    }

    function addOption($value, $title){
        $this->setArray('options', $value, $title);

        return $this;
        return formV2Radio();
    }

}

