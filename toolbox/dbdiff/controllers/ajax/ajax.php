<?php
namespace toolbox;
class ajax_controller {

    static function setup(){

    }


    static function passThru(){
        page::get()
            ->setMainView('main/ajax.php');


/*errorHandler::get()->setCallback(function($e){
    $print_error = false;

    try{
        if(config::get()->getConfig('environment') !== 'production'
            || (
                accessControl::get()->hasRequirement('admin')
            )
        ){

            $print_error = true;

        }
    }catch(Exception $e2){

    }

    if(headers_sent()){


        if($print_error){
            die('<script></script><span style="color: red; font-weight: bold; font-size: 14px;">Unexpected Error.
            Admin-only message: '.$e->getMessage().'.</span>');
        }

        die('<script></script><span style="color: red; font-weight: bold; padding: 5px; display: block; text-align: center;">
        There was an unexpected error. We have logged this issue and will look into it. Please try again later.</span>');
    }

        if($print_error){
            messages::setErrorMessage('Admin-only message: '.$e->getMessage());
        }else{
        	messages::setErrorMessage('There was an unexpected error.
        	Please try again later or contact suppor if problem persisits.');
        }


        router::get()->toInternal('js_error');
        exit;
});*/


    }

}
