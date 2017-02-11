<?php
namespace toolbox;

class formTextArea extends form{
    protected $type,
            $name,
            $value,
            $label,
            $disabled = false,
            $placeholder,
            $columns = 6,
            $rows = 4,
            $style,
            $size,
            $class,
            $container_class,
            $wrapper_style,
            $checked;

    function __construct($class=null){
        $this->container_class = $class;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
        return new formTextArea();
    }

    public function setClass($class){
        $this->class = $class;
        return $this;
        return new formTextArea();
    }

    public function setValue($value){
        $this->value = $value;
        return $this;
        return new formTextArea();
    }

    public function setLabel($label){
        $this->label = $label;
        if($this->name === null){
            $this->setName(utils::toSlug($label));
        }
        return $this;
        return new formTextArea();
    }

    public function setDisabled($disabled = true){
        $this->disabled = $disabled;
        return $this;
        return new formTextArea();
    }

    public function setPlaceholder($placeholder){
        $this->placeholder = $placeholder;
        return $this;
        return new formTextArea();
    }

    public function setColumns($num){
        $this->columns = $num;
        return $this;
        return new formTextArea();
    }

    public function setRows($num){
        $this->rows = $num;
        return $this;
        return new formTextArea();
    }

    public function setAutoComplete($autocomplete){
        if($autocomplete === false){
            $autocomplete = "off";
        }
        $this->autocomplete = $autocomplete;
        return $this;
        return new formTextArea();
    }

    public function setInputStyle($style){
        $this->style = $style;
        return $this;
        return new formTextArea();
    }

    public function setNote($note){
        $this->note = $note;
        return $this;
        return new formTextArea();
    }

    public function setWrapperStyle($style){
        $this->wrapper_style = $style;
        return $this;
        return new formTextArea();
    }



    protected function preRender(){

        if($this->label === null){
            $this->label = ucfirst($this->name);
        }
        if($this->placeholder === null && $this->label !== null){
            $this->placeholder = '' . $this->label;
        }

    }


}