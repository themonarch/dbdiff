<?php
namespace toolbox;
class password_reset_controller {

    static function setup(){
        accessControl::get();

    }

    public function __construct() {

        if($_SERVER['REQUEST_METHOD'] !== 'POST' ){
            return $this->invalid();
        }


        validator::setForm($_POST);
        validator::validate('password');
        validator::validate('password2');
        if(!validator::isValid()){
            return $this->invalid();
        }

        db::query('update `users` set `password_hash` = '.db::quote(utils::hash($_POST['password'])).'
        where `user_id` = '.$user->getID());

        //succes
        messages::setSuccessMessage('Your password has now been changed.', 'reset_password');
        return page::get()->addview(function(){ ?>
            <div class="section centered colored padded">
                <div class="contents">
                    <div class="contents-inner">
                        <div class="section-header">
                            <h2>Password Recovery</h2>
                        </div>
                        <div class="section-content">
                            <?php messages::printMessages(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-close-overlay="" class="close_overlay"></div>
            <?php
        });


    }

    public function invalid($msg = null){
        form::storeValues($_POST);
        if($msg !== null)
            messages::setErrorMessage($msg, 'reset_password');
        page::get()
            ->addView('elements/password_reset_form.php', 'content');
    }



}
