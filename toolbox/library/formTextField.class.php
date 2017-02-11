<?php
namespace toolbox;

class formTextField extends form{
    protected $type,
            $name,
            $value,
            $label,
            $disabled = false,
            $placeholder,
            $note,
            $maxlength,
            $max_width,
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
        return new formTextField();
    }


    public function setTypeText(){
        $this->type = 'text';
        return $this;
        return new formTextField();
    }

    public function setTypeButton(){
        $this->type = 'button';
        return $this;
        return new formTextField();
    }

    public function setTypeCheckbox(){
        $this->type = 'checkbox';
        return $this;
        return new formTextField();
    }

    public function setTypeHidden(){
        $this->type = 'hidden';
        return $this;
        return new formTextField();
    }

    public function setTypePassword(){
        $this->type = 'password';
        return $this;
        return new formTextField();
    }

    public function setTypeRadio(){
        $this->type = 'radio';
        return $this;
        return new formTextField();
    }

    public function setTypeSubmit(){
        $this->type = 'submit';
        return $this;
        return new formTextField();
    }

    public function setClass($class){
        $this->class = $class;
        return $this;
        return new formTextField();
    }

    public function setValue($value){
        $this->value = $value;
        return $this;
        return new formTextField();
    }

    public function setLabel($label){
        $this->label = $label;
        if(!isset($this->name)){
            $this->setName(utils::toSlug($label));
        }
        return $this;
        return new formTextField();
    }

    public function setDisabled($disabled = true){
        $this->disabled = $disabled;
        return $this;
        return new formTextField();
    }

    public function setPlaceholder($placeholder){
        $this->placeholder = $placeholder;
        return $this;
        return new formTextField();
    }

    public function setNote($note){
        $this->note = $note;
        return $this;
        return new formTextField();
    }

    public function setMaxLength($maxlength){
        $this->maxlength = $maxlength;
        return $this;
        return new formTextField();
    }

    public function setMaxWidth($max_width){
        $this->max_width = $max_width;
        return $this;
        return new formTextField();
    }

    public function setAutoComplete($autocomplete){
        if($autocomplete === false){
            $autocomplete = "off";
        }
        $this->autocomplete = $autocomplete;
        return $this;
        return new formTextField();
    }

    public function setInputStyle($style){
        $this->style = $style;
        return $this;
        return new formTextField();
    }

    public function setWrapperStyle($style){
        $this->wrapper_style = $style;
        return $this;
        return new formTextField();
    }

    public function setSize($size){
        $this->size = $size;
        return $this;
        return new formTextField();
    }


    public function setChecked($checked = true){
        $this->checked = $checked;
        return $this;
        return new formTextField();
    }


    protected function preRender(){

        if($this->label === null && $this->type !== 'hidden'){
            $this->label = ucfirst($this->name);
        }
        if($this->placeholder === null && $this->label !== null){
            $this->placeholder = '' . $this->label;
        }

    }


}