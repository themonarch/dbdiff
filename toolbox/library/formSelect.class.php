<?php
namespace toolbox;

class formSelect extends form{
    protected $type,
            $name,
            $value,
            $label,
            $disabled = false,
            $size,
            $class,
            $container_class,
            $note,
            $wrapper_style,
            $checked,
            $attributes,
            $options = array();

    function __construct($class=null){
        $this->container_class = $class;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
        return new formSelect();
    }

    public function setClass($class){
        $this->class = $class;
        return $this;
        return new formSelect();
    }

    public function setCustomAttributes($attributes){
        $this->attributes = $attributes;
        return $this;
        return new formSelect();
    }

    public function setNote($note){
        $this->note = $note;
        return $this;
        return new formSelect();
    }

    public function addOption($name, $value = null){
        if($value === null){
            $value = utils::toSlug($name);
        }
    	if(isset($this->options[$value])){
    		unset($this->options[$value]);
    	}
        $this->options[$value] = $name;
        return $this;
        return new formSelect();
    }

    public function setValue($value){
        $this->value = $value;
        return $this;
        return new formSelect();
    }

    public function setLabel($label){
        $this->label = $label;
        if($this->name === null){
            $this->setName(utils::toSlug($label));
        }
        return $this;
        return new formSelect();
    }

    public function setDisabled($disabled = true){
        $this->disabled = $disabled;
        return $this;
        return new formSelect();
    }


    public function setAutoComplete($autocomplete){
        if($autocomplete === false){
            $autocomplete = "off";
        }
        $this->autocomplete = $autocomplete;
        return $this;
        return new formSelect();
    }

    public function setInputStyle($style){
        $this->style = $style;
        return $this;
        return new formSelect();
    }

    public function setWrapperStyle($style){
        $this->wrapper_style = $style;
        return $this;
        return new formSelect();
    }



    protected function preRender(){

        if($this->label === null){
            $this->label = ucfirst($this->name);
        }

    }


}