<?php
namespace toolbox;

class formV2Choosefield extends formV2 {

    function __construct(){
        parent::__construct();
        $this->setMainView('/formV2/choosefield.php');
        $this->setRemove(false);
    }

    function setAjaxUrl($url, $overlay_id){
    	$this->set('overlay_id', $overlay_id);

        $extra_params = 'field_id='.urlencode($this->name);
        if($this->remove){
            $extra_params .= '&remove=true';
        }else{
            $extra_params .= '&remove=false';
        }

        if(strpos($url, '?') === false){
           $url = $url.'?'.$extra_params;
        }else{
           $url = $url.'&'.$extra_params;
        }

        $this->set('ajax_url', $url);
        return $this;
        return formV2Choosefield();
    }

    function setValueDisplayName($name){
        $this->set('value_display_name', $name);
        return $this;
        return formV2Choosefield();
    }

    function setRemove($bool){
        $this->set('remove', $bool);
        return $this;
        return formV2Choosefield();
    }




}

