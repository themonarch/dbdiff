<?php
namespace toolbox;
class tokenHelper {

    private $token_type;
    private $user_object;
    private $token_data;
    function __construct(user $user_object, $token_type){

        $this->token_type = $token_type;
        $this->user_object = $user_object;

        if($this->token_type === 'email_verification'){

            //make sure user not already verified
            if($this->user_object->isEmailValidated()){
                throw new userException("This email is already validated.", 1);
            }

            //make sure user isn't over limit
            if($this->isOverLimit()){
                //don't send
                throw new userException("Please wait at least 24 hours to request another verification email.
                If you are experiencing issues with your account please contact us.", 1);
            }

            //check for existing unused one
            if(!$this->token_data = $this->getUnusedToken()){
                //generate new one
                $this->token_data = $this->generateNewToken();
            }

            //increase count
            $this->updateCounts();

        }elseif($this->token_type === 'email_change'){
            $new_email = $this->user_object->getCustomValue('email_change');

            if($new_email === null){
                throw new toolboxException("No email address to change to.", 1);
            }

            //make sure new email set and different from old
            if($this->user_object->getEmail() === $new_email){
                //don't send
                $this->user_object->deleteValue('email_change', $new_email);
                throw new userException("Change to email is same as current email address.", 1);
            }

            if(db::query('select count(*) as `count` from `users` where `email_address` = '.db::quote($new_email))
                ->fetchRow()->count > 0
            ){
                $this->user_object->deleteValue('email_change', $new_email);
                throw new userException("This email address is already associated with another account.", 1);
            }

            //make sure user isn't over limit
            if($this->isOverLimit()){
                //don't send
                throw new userException("Please wait at least 24 hours to request another email change.
                If you are experiencing issues with your account please contact us.", 1);
            }

            //invalidate all other tokens
            db::query('delete from `user_tokens` where `user_id` = '.$this->user_object->getID());

            //generate new one
            $this->token_data = $this->generateNewToken();

            //increase count
            $this->updateCounts();


        }elseif($this->token_type === 'password_reset'){
            //make sure user isn't over limit
            if($this->isOverLimit()){
                //don't send
                throw new userException("Please wait at least 24 hours to request another password reset email.
                If you are experiencing issues with your account please contact us.", 1);
            }

            //check for existing unused one
            if(!$this->token_data = $this->getUnusedToken()){
                //generate new one
                $this->token_data = $this->generateNewToken();
            }

            //increase count
            $this->updateCounts();


        }elseif($this->token_type === 'quick_login'){

            //check for existing unused one
            if(!$this->token_data = $this->getUnusedToken()){
                //generate new one
                $this->token_data = $this->generateNewToken();
            }

        }else{
            throw new toolboxException('Unexpected token type: '.$this->token_type, 1);
        }

    }


    function getToken(){
        return $this->token_data->token;
    }

    private $sent_count;
    private function getSentCount(){
        if($this->sent_count === null){
            $this->sent_count = (int)$this->user_object->getCustomValue($this->token_type.'_sent_count');
        }
        return $this->sent_count;
    }

    private function isOverLimit(){
        if(
            //if already sent 5 or more times
            $this->getSentCount() >= 5
            //within last 24 hrs
            && strtotime($this->user_object->getCustomValue($this->token_type.'_sent_date')) > strtotime('-24 hours')
        ){
            return true;
        }

        return false;

    }

    function getUnusedToken(){
        $token = db::query('select * from `user_tokens`
        where `user_id` = '.$this->user_object->getID().'
        and `token_type` = '.db::quote($this->token_type).'
        and `used` = 0')->fetchRow();
        if($token){
            //update date
            db::query('update `user_tokens` set `date_sent` = now()
                        where `id` = '.$token->id);
        }

        return $token;
    }

    function generateNewToken(){
        db::query('INSERT INTO `user_tokens` (
            `user_id`,
            `token`,
            `token_type`
        ) VALUES (
            '.$this->user_object->getID().',
            '.db::quote(utils::getRandomString(10)).',
            '.db::quote($this->token_type).'
        ) ON DUPLICATE KEY UPDATE `used` = 0, `token` = '.db::quote(utils::getRandomString(10)));

        return $this->getUnusedToken();
    }


    private function updateCounts(){
        if($this->getSentCount() >= 5){//reset bc more than 24hrs since first
            $this->user_object->setCustomValue($this->token_type.'_sent_count', 1);
            $this->user_object->setCustomValue($this->token_type.'_sent_date', date('Y-m-d H:i:s'));
        }elseif($this->getSentCount() === 0){//set initial values
            $this->user_object->setCustomValue($this->token_type.'_sent_count', 1);
            $this->user_object->setCustomValue($this->token_type.'_sent_date', date('Y-m-d H:i:s'));
        }else{//increase count
            $this->user_object->setCustomValue($this->token_type.'_sent_count', $this->getSentCount()+1);
        }
    }


}
