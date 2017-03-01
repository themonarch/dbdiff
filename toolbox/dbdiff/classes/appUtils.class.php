<?php
namespace toolbox;
class appUtils {

    static function sendEmail($email_address, $subject_line, $body_content, $from = null){

        if(is_array($email_address)){
            $email_to = implode(', ', $email_address);
        }else{
            $email_to = $email_address;
        }

		if(!appUtils::isProduction()){
			utils::logNonFatalError('Would have sent email: ' .$body_content);
			return;
		}

        if($from == null){
            $from = config::get()->getConfig('app_name')." <support@".config::get()->getConfig('HTTP_HOST').'>';
        }else{
            $from = $from;
        }

        mail($email_to,$subject_line,$body_content,$from);
        return;

		utils::sendEmail($email_to, $subject_line, $body_content
                    .'<br><br>Thanks,'
                    .'<br>'.config::get()->getConfig('app_name').' Team'
                    .'<br>'.config::get()->getConfig('HTTP_PROTOCOL').'://'.config::get()->getConfig('HTTP_HOST').'',
                    "From: ".$from."\r\n"
                    . 'MIME-Version: 1.0' . "\r\n"
                    . 'Content-type: text/html; charset=iso-8859-1');

    }

    static function isProduction(){
        return (config::get()->getConfig('environment') == 'production');
    }



    static $user_groups = array();
    static function getUserGroupID($group_name){
        if(!isset(self::$user_groups[$group_name])){
            $group = db::query('select * from `user_groups` where `name` = '.db::quote($group_name).'')->fetchRow();
            if($group === null){
                throw new toolboxException('No user group found with name: '.$group_name, 1);
            }
            self::$user_groups[$group_name] = $group;
            self::$user_group_ids[$group->group_id] = $group;
        }

        return self::$user_groups[$group_name]->group_id;
    }

    static $user_group_ids = array();
    static function getUserGroupName($group_id){
        if(!isset(self::$user_group_ids[$group_id])){
            $group = db::query('select * from `user_groups` where `group_id` = '.db::quote($group_id).'')->fetchRow();
            if($group === null){
                throw new toolboxException('No user group found with id: '.$group_id, 1);
            }
            self::$user_group_ids[$group_id] = $group;
            self::$user_group_ids[$group->name] = $group;
        }

        return self::$user_group_ids[$group_id]->name;
    }

}
