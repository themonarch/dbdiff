<?php
namespace toolbox;
/**
 * Validates a form submission
 */
 class validator{
    private static $all_valid = true;
    private static $form = array();


    public static function setForm(&$form){
        utils::trimArray($form);
        self::$form = &$form;
    }
    /**
     * returns true if the current from had NO errors
     */
    public static function isValid(){
    	if(!self::$all_valid && self::$auto_restore_form){
            formV2::storeValues();
    	}
        return self::$all_valid;
    }

	private static $auto_restore_form = false;
	static function autoRestoreForm($bool){
		self::$auto_restore_form = $bool;
	}


    /**
     *
     */
    public static function validate($field_name, $validator = null, $msg_override = null){
        $msg = 'Please enter a value for this field.';

		$index = trim(utils::getStringBetweenTwoStrings($field_name, '[', ']'));
		if($index !== ''){
			$field_name = utils::removeStringAfterString($field_name, '[');
		}

        if($validator === null){
            $validator = $field_name;
        }

        if(isset(self::$form[$field_name]) && is_array(self::$form[$field_name])){
            $return = true;
            foreach (self::$form[$field_name] as $key => $value) {
            	if($index !== '' && $key != $index){
            		continue;
            	}



            if(is_callable($validator)){

                $param_arr = func_get_args();
                $param_arr[0] = isset(self::$form[$field_name][$key]) ? self::$form[$field_name][$key] : null;
                array_splice($param_arr, 1, 2);

                if(($msg = call_user_func_array($validator, $param_arr)) !== true && $msg !== null){
                    if($msg_override === null){
                        $msg_override = $msg;
                    }
                    messages::setWrong($msg_override, $field_name, $key);
                    self::$all_valid = false;
                    $return = false;
                }

            }elseif(!isset(self::$form[$field_name][$key])
                || (($msg = isValid::$validator(self::$form[$field_name][$key])) !== true && $msg !== null)){
                    if($msg_override === null){
                        $msg_override = $msg;
                    }
                    messages::setWrong($msg_override, $field_name, $key);
                self::$all_valid = false;
                $return = false;
            }


            }

            return $return;
        }




        if(is_callable($validator)){
            $param_arr = func_get_args();
            $param_arr[0] = isset(self::$form[$field_name]) ? self::$form[$field_name] : null;
            array_splice($param_arr, 1, 2);

            if(($msg = call_user_func_array($validator, $param_arr)) !== true && $msg !== null){
                if($msg_override === null){
                    $msg_override = $msg;
                }
                messages::setWrong($msg_override, $field_name);
                self::$all_valid = false;
                return false;
            }

        }elseif(!isset(self::$form[$field_name])
            || (($msg = isValid::$validator(self::$form[$field_name])) !== true && $msg !== null)){
                if($msg_override === null){
                    $msg_override = $msg;
                }
                messages::setWrong($msg_override, $field_name);
            self::$all_valid = false;
            return false;
        }

        return true;
    }

    /**
     *
     */
    public static function validateAllowEmpty($field_name, $validator = null){
        if(!isset(self::$form[$field_name]) || trim(self::$form[$field_name]) == ''){
            return false;
        }

        return self::validate($field_name, $validator);

    }

 }

