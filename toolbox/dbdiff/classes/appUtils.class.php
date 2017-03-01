<?php
namespace toolbox;
class appUtils {

    static function sendEmail($to, $subject, $message, $from = null){

        if(is_array($to)){
            $to = implode(', ', $to);
        }

        if($from == null){
            $from = config::get()->getConfig('app_name')." <contact@".config::get()->getConfig('HTTP_HOST').'>';
        }

        // To send HTML mail, the Content-type header must be set
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';

        // Additional headers
        $headers[] = 'To: '.$to;
        $headers[] = 'From: '.$from;
        //$headers[] = 'Cc: birthdayarchive@example.com';
        //$headers[] = 'Bcc: birthdaycheck@example.com';


        if(!appUtils::isProduction()){
            utils::logNonFatalError('Would have sent email: ' .$message);
            return;
        }

        // Mail it
        if(!mail($to, $subject, $message, implode("\r\n", $headers))){
            throw new toolboxError('Failed to send email: '
                .utils::array2string(array($to, $subject, $message, implode("\r\n", $headers))));
        }

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
