<?php namespace toolbox;
//if session expired
if(!isset($_COOKIE['app_session_track'])){
    //create new session token
    cookieHelper::create()
        ->setCookieDomainToAllSubdomains()
        ->setCookieName('app_session_track')
        ->setCookieValue(1)
        ->setCookieExpirationToMinutes(15)
        ->sendCookieToUser();

    //save session stats
    $sql = 'INSERT INTO `user_session_tracking` (`user_id`, `token_id`)
    VALUES ('.$row->user_id.', '.$row->session_id.')';
    db::query($sql);

    user::getUserLoggedIn()
    	->logActivity('session', 'User logged in or is continuing previous session. (Sessions are checked every 15 minutes).');

}