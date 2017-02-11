<?php
namespace toolbox;

class formV2Select extends formV2 {

    function __construct(){
        parent::__construct();
        $this->setMainView('/formV2/select.php');
        $this->setChecked(false);
    }

    function setChecked($boolean){
        $this->set('checked', $boolean);

        return $this;
        return formV2Select();
    }

    public function addOptionGroup($name, $value = null, $option_group = null){
        if($value === null){
            $value = utils::toSlug($name);
        }

        if($option_group !== null){

            if(!isset($this->options[$option_group])){
                $this->options[$option_group] = array();
            }elseif(!is_array($this->options[$option_group])){
                throw new toolboxException('trying to add an optgroup with same name as existing value.', 1);
            }

            $this->options[$option_group][] = $value;
            return $this;

        }

        if(isset($this->options[$value])){
            unset($this->options[$value]);
        }
        $this->options[$value] = $name;
        return $this;
        return new formSelect();
    }

    function addOption($value, $title, $attributes = '', $optgroup = null){
        if($optgroup !== null){
            if(!isset($this->options[$optgroup])){
                $this->options[$optgroup] = array();
            }

            $this->options[$optgroup][$value] = array('title' => $title, 'attributes' => $attributes, 'optgroup' => $optgroup);
            //$this->options_attributes[] = array($value=>$title);

            return $this;

        }

        $this->options[] = array($value=>array('title' => $title, 'attributes' => $attributes));
        //$this->options_attributes[] = array($value=>$title);

        return $this;
        return formV2Select();
    }

}

