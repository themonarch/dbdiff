<?php
namespace toolbox;

class formV2Textarea extends formV2 {

    function setRows($int){
        $this->set('rows', $int);

        return $this;
        return new formV2();
    }

    function setCols($int){
        $this->set('columns', $int);

        return $this;
        return new formV2();
    }

    function __construct(){
        parent::__construct();
        $this->setMainView('/formV2/textarea.php');
    }


}

