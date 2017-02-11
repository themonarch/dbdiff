<?php
namespace toolbox;

class formV2Textfield extends formV2 {

    function __construct(){
        parent::__construct();
        $this->setMainView('/formV2/textfield.php');
        $this->setTypeText();
    }

    function setTypeText(){
        $this->set('type', 'text');
        return $this;
        return formV2Textfield();
    }

    function setTypePassword(){
        $this->set('type', 'password');
        return $this;
        return formV2Textfield();
    }

    function setTypeBlank(){
        $this->set('type', 'blank');
        return $this;
        return formV2Textfield();
    }

    function setTypeFile(){
        $this->set('type', 'file');
        return $this;
        return formV2Textfield();
    }

    function setPlaceholder($placeholder){
        $this->set('placeholder', $placeholder);
        return $this;
        return formV2Textfield();
    }

}
