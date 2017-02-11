<?php
namespace toolbox;

class formV2Checkbox extends formV2 {

    function __construct(){
        parent::__construct();
        $this->setMainView('/formV2/checkbox.php');
        $this->setChecked(false);
    }

    function setChecked($boolean){
        $this->set('checked', $boolean);

        return $this;
        return formV2Checkbox();
    }

    function setLabelPre($pre_label){
        $this->set('pre_label', $pre_label);

        return $this;
        return formV2Checkbox();
    }

    function isChecked(){
        return $this->checked;
    }


}

