<?php
namespace toolbox;
class datasetBuilder {
    function __construct(){

    }
    private $data = array('Column' => array());

    function set($field, $value){
        $this->storeValue($value, 'set'.ucfirst($field));
        return $this;
        return new datasetBuilder();
    }
	
	private $array_key_current;
	private $array_field_current;
    function setArray($array_key, $value){
    	$this->array_field_current = $value;
    	$this->array_key_current = $array_key;
        $this->storeValue($value, 'set'.ucfirst($array_key), true);
        return $this;
        return new datasetBuilder();
    }
	
    private $column_meta_data = array();
    function setArrayMeta($key, $value){
        if($this->array_key_current === null || $this->array_field_current === null ){
            throw new datasetBuilderException('Tried to set meta data before a column was set.', 1);
        }
		
        if(isset($this->column_meta_data[$this->array_key_current][$this->array_field_current][$key])){
            throw new datasetBuilderException('Column meta data '.$key.' for ' .$this->array_field_current.' in '.$this->array_key_current.' was already set to '
            	.$this->column_meta_data[$this->array_key_current][$key].' for '
            	. $key . '. Can\'t set to '.$value.'.', 1);
        }
        $this->column_meta_data[$this->array_key_current][$this->array_field_current][$key] = $value;
        return $this;
        return new datasetBuilder();
    }
	
    private function storeValue($value, $function, $array = false){
        $function = utils::removeStringFromBeginning($function, 'set');
        if($array){
            $this->data[$function][] = $value;
        }else{
            $this->data[$function] = $value;
        }
        return $this;
        return new datasetBuilder();
    }
	
    function get($field){
        return $this->getValue('get'.ucfirst($field));
    }
	
    private function getValue($function){
        $function = utils::removeStringFromBeginning($function, 'get');
		if(!isset($this->data[$function]) && isset($this->data['Metric'][0]->data[$function])){
			return $this->data['Metric'][0]->data[$function];
		}elseif(isset($this->data[$function])){
        	return $this->data[$function];
		}else{
			return null;
		}
    }
	
    function getArrayMeta($array_name, $field_name, $key){
		return $this->column_meta_data[$array_name][$field_name][$key]; 
    }

    /**
     * Creation factory
     * (creates NEW instance each time)
     */
    static function create(){
        return new self();
        return new datasetBuilder();
    }

}

class datasetBuilderException extends toolboxException{};