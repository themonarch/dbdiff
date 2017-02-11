<?php
namespace toolbox;
class choose_input_contents_controller {

    public function __construct() {
        usleep(500000);

        $field = formV2::choosefield()
            ->setName($_POST['name'])
            ->setValue(utils::htmlEncode($_POST['value']))
            ->setRemove(($_POST['remove'] == 'true') ? true : false)
            ->setAjaxUrl($_POST['ajax_url'], $_POST['overlay_id'])
            ->setValueDisplayName($_POST['value_display_name']);

        if(isset($_POST['label'])){
            $field->setLabel($_POST['label']);
        }

        page::get()->addView($field, 'content');

    }

    static function passThru(){
        accessControl::get()
            ->removeRequired('member');
    }

}
