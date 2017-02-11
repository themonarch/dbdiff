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

    function grantAccessToClient($client_id, $property){
        $this->setCustomValue($property.'-'.$client_id, date('Y-m-d H:i:s'));
    }

    function hasAccessToClient($client_id, $property){

        user::getUserLoggedIn()->logActivity('client-unlocked',
            'Unlocked client: '.$property.':'.$client_id);

        //users with special access don't need to unlock
        if($this->hasGrant('client_lock_bypass')){
            return true;
        }

        try{
            $date = $this->getCustomValue($property.'-'.$client_id);
        }catch(whmcsClientException $e){
            return false;
        }

        if(strtotime($date) > strtotime('-30 minutes')){
            return true;
        }

        return false;

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

    static function isUserLoggedIn(){
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
