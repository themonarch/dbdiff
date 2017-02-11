<?php namespace toolbox;
class formV2 extends page{

    function __construct(){
        parent::__construct(false);
		$this->setDisabled(false);
        $this->set('wrapper_class', '');
        $this->setInputClass('');
        $this->setInputRowStyle('');
        $this->setInputWrapperClass('');
        $this->set('label', '');
        $this->set('value', '');
    }

    function renderViews($group = 'automatically-rendered', $reset = false){
        $this->readValues();
        parent::renderViews($group);
    }

    function setDisabled($bool = true){
        $this->set('disabled', $bool);

        return $this;
        return new formV2();
    }

    function setLabel($label){
        $this->set('label', $label);

        return $this;
        return new formV2();
    }

    function setStyle($style){
        $this->set('style', $style);

        return $this;
        return new formV2();
    }

    function setStyleMsg($style){
        $this->set('style_msg', $style);

        return $this;
        return new formV2();
    }

    function setInputClass($class){
        $this->set('class', $class);

        return $this;
        return new formV2();
    }

    function setEditUrl($url){
        $this->set('edit_url', $url);

        return $this;
        return new formV2();
    }

    function setNote($note){
        $this->set('note', $note);

        return $this;
        return new formV2();
    }

    function setMaxWidth($width){
        $this->set('max_width', $width);

        return $this;
        return new formV2();
    }

    function setInputWrapperClass($string){
        $this->set('input_wrapper_class', $string);

        return $this;
        return new formV2();
    }

    function setInputRowStyle($string){
        $this->set('input_row_style', $string);

        return $this;
        return new formV2();
    }

    function setSize($width){
        $this->set('size', $width);

        return $this;
        return new formV2();
    }

    private static $index_counters = array();
    function setName($name){
        if(get_called_class() != 'toolbox\formV2Checkbox' && strpos($name, '[]') !== false){
            $name = utils::removeStringFromEnd($name, '[]');
            if(!isset(self::$index_counters[$name])){
                self::$index_counters[$name] = 0;
            }

            $name = $name.'['.self::$index_counters[$name]++.']';

        }
        $this->set('name', $name);

        return $this;
        return new formV2();
    }

    function setValue($value){
        //$this->set('value', utils::htmlEncode($value));//NOT NECCESSARY FOR INPUT FIELDS
        $this->set('value', $value);

        return $this;
        return new formV2();
    }

    function getValue(){
        return $this->value;
    }

    function setCustomAttributes($custom_attributes){
        $this->set('custom_attributes', $custom_attributes);

        return $this;
        return new formV2();
    }

    public static function storeValues($postdata = null){
        if($postdata === null){
            $postdata = &$_POST;
        }
        if(isset($postdata['widget_data'])){
            unset($postdata['widget_data']);
        }

        if(isset($postdata['remove'])){
            $field = explode('[', $postdata['remove']);
            $field = $field[0];
            $index = utils::getStringBetweenTwoStrings($postdata['remove'], $field.'[', ']');

            unset($postdata[$field][$index]);

        }elseif(isset($postdata['add'])){
            $postdata[$postdata['add']][] = '';
        }




        foreach($postdata as $key => $value){
            if(is_array($value)){
                foreach ($value as $key2 => $value2) {
                    messages::setCustomMessage($value2, 'form', $key, $key2);
                }
            }else{
                messages::setCustomMessage($value, 'form', $key);
            }
        }

    }

    /**
     * Set post values from cookies + error messages
     */
    function readValues(){
        $cookie_name = $this->name;
        //if input is an array, separate the input name and index
        if(strpos($cookie_name, '[') !== false){
            $cookie_name = utils::removeStringAfterString($this->name, '[');
            $cookie_index = utils::removeStringFromBeginning($this->name, $cookie_name.'[');
            $cookie_index = utils::removeStringFromEnd($cookie_index, ']');


            $value = messages::readMessages('form', $cookie_name);
            $display_name = messages::readMessages('form', 'display_name-'.$cookie_name);

            //if no index specified
            if($cookie_index == ''){
                if(is_array($value)){
                    $value = in_array($this->value, $value) ? $this->value : null;
                }else{
                    $value = null;
                }
            }elseif(isset($value[$cookie_index])){
                $value = $value[$cookie_index];
                if(isset($display_name[$cookie_index])){
                    $display_name = $display_name[$cookie_index];
                }else{
                    $display_name = '';
                }
            }else{
                $value = null;
                $display_name = null;
            }



        }else{
            $value = messages::readMessages('form', $cookie_name);
            $display_name = messages::readMessages('form', 'display_name-'.$cookie_name);
        }




        $this->error = messages::readMessages('wrong', $cookie_name);
        //utils::vd($this->error);
        if(isset($cookie_index) && $cookie_index !== ''){
            $this->error = isset($this->error[$cookie_index]) ? $this->error[$cookie_index] : null;
        }

        if($this->error !== null){
            $this->wrapper_class = ' error';
        }

        if($value !== null){
            $this->setValue(htmlspecialchars($value));
            if(method_exists($this, 'setChecked')){
                $this->setChecked(true);
            }
        }

        if(isset($display_name) && $display_name !== ''){
            $this->setValueDisplayName($display_name);
        }

        return $this;
        return new formV2();

    }


    static function checkbox(){
        return new formV2Checkbox();
    }

    static function radio(){
        return new formV2Radio();
    }

    static function select(){
        return new formV2Select();
    }

    static function textfield(){
        return new formV2Textfield();
    }

    static function textarea(){
        return new formV2Textarea();
    }

    static function choosefield(){
        return new formV2Choosefield();
    }

}
