<?php
namespace toolbox;
class js_error_internal {

    static function setup(){
        router::get()
            ->setMaxParams(false)
            ->setMinParams(false);
        accessControl::get()
            ->clearRequirements();
    }

    function __construct(){
        $page = page::get()
                ->clearViews()
                ->set('error', config::get()->getConfig('app_name').' error:')
                ->addView(function($tpl){ ?>
                    console.log(<?php echo json_encode($tpl->error); ?>);
                <?php });

        $error = messages::readMessages();

        if(isset($error['error']) && isset($error['error'][0])){
             $page->set('error', $page->error.' '.$error['error'][0]);
        }



    }

}
