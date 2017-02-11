<?php
namespace toolbox;
abstract class form{
    protected $wrapper_class = '';

    public function render(){
        $this->readValues();
        $this->preRender();
        extract(get_object_vars($this), EXTR_REFS);
        $called_class = explode('\\', get_called_class());
        require toolbox::getPath().'/'.toolbox::get()->getAppFolderName().'/templates/widgets/form/'.end($called_class).'.php';
    }

    /**
     * set defaults and setup logic
     */
    abstract protected function preRender();

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

    public static function storeValues(){
        foreach($_POST as $key => $value){
            //forget the passwords
            /*if(strpos($key, 'password') === 0){
                continue;

            }*/
            if(is_array($value)){
	            messages::setCustomMessage($key.'['.key($value).']', 'form', current($value));
            }else{
            	messages::setCustomMessage($value, 'form', $key);
			}
        }
    }

    public static function textField($class=null){
        return new formTextField($class);
    }

    public static function textArea($class=null){
        return new formTextArea($class);
    }

    public static function select($class=null){
        return new formSelect($class);
    }

    public static function radio($class=null){
        return new formRadio($class);
    }


}