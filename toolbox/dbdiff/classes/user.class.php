<?php
namespace toolbox;
class user extends user_framework {

    function __construct($user_id, $is_string_id = false){
        parent::__construct($user_id, $is_string_id);
    }



    /**
     * returns first and abbreviated last name of user.
     */
    function getNameAbbr(){
        return utils::htmlEncode($this->getCustomValue('first_name')
        	.' '.substr($this->getCustomValue('last_name'), 0, 1).'.');
    }

    function getName(){
        return utils::htmlEncode($this->getCustomValue('first_name').' '.$this->getCustomValue('last_name'));
    }

    function getAccountStatus(){
        $account_status = $this->getCustomValue('account_status');
        if($account_status === null){
            $account_status = 'pending';
        }

        return $account_status;
    }


    function setAccountStatus($account_status){

        if(!in_array($account_status, array('active', 'pending', 'suspended'))){
            throw new userException("Can't set account status, invalid status given: ".$account_status, 1);
        }

        if($account_status === 'active' && $this->getAccountStatus() != $account_status){
            appUtils::sendEmail($this->getEmail(), 'Your Account Has Been Approved',
                'Dear '.$this->getCustomValue('first_name').',
                <br><br>Your account on '.config::get()->getConfig('app_name').'
                has been approved and ready to use.
                <br><br>Your username: '.$this->getEmail().'
                <br><br>Link to log in: '.config::get()->getConfig('HTTP_PROTOCOL').'://'.config::get()->getConfig('HTTP_HOST').'/');
        }
        $this->setCustomValue('account_status', $account_status);

    }

    function getSyncProfile($profile_id){
		try{
			$sync = sync::get($profile_id);
		}catch(toolboxException $e){
			throw new userException('Sync profile does not exist!');
		}

		if(!$sync->belongsTo($this->getID())){
			throw new userException('Sync profile is not accessable by this user.');
		}
		return $sync;
		return new sync();//type hint for IDEs
    }

    function logActivity($type, $log){
        db::query('INSERT INTO `user_activity_log` (`user_id`, `type`, `action`)
            VALUES ('.$this->getID().', '.db::quote($type).', '.db::quote($log).');');

    }

    static function isMemberLoggedIn(){
        if(parent::isUserLoggedIn()){
            if(self::getUserLoggedIn()->hasGrant('member')){
                return true;
            }
        }

        return false;
    }

    static function isGuestLoggedIn(){
        if(parent::isUserLoggedIn()){
            if(!self::getUserLoggedIn()->hasGrant('member')){
                return true;
            }
        }

        return false;
    }

}
