<?php
namespace toolbox;

$error_handler = errorHandler::get();

//don't log soft exceptions (nor email notifications)
$error_handler->setExceptionNoLogging('toolbox\softPublicException');

class publicException extends toolboxException {}//user-end viewable errors (printed on screen)
class softPublicException extends publicException {}//user-end viewable (printed on screen, not logged or emailed)

//if there is any type of fatal error, show a default error view to user.
errorHandler::get()->addCallback(function($e){

    $display_error = false;//controls whether to show the error message on screen

    if(//don't display error to public
        config::getSetting('environment') !== 'production'
        || accessControl::get()->hasRequirement('admin')
    ){
        $display_error = true;
    }

    if(//if user readable msg, display it
        get_class($e) === 'toolbox\publicException'
        || get_class($e) === 'toolbox\softPublicException'
    ){
        $display_error = true;
    }

    if(headers_sent()){
        if($display_error){
            die('<script></script><span style="color: red; font-weight: bold; padding: 5px; '
                .'display: block; text-align: center;">'
                .'Unexpected Error. Admin-only message: '
                .$e->getMessage().'.</span>');
        }

        die('<script></script><span style="color: red; font-weight: bold; padding: 5px; '
        .'display: block; text-align: center;">'
        .'There was an unexpected error. We have logged this issue and will look into it. Please try again later.</span>');
    }

    if($display_error){
        header('X-Error_msg: '. utils::removeNewLines($e->getMessage()));
        page::get()
            ->set('error', $e->getMessage());
    }else{
        header('X-Error_msg: '. 'An unexpected error has occured. Please try again later or contact staff if '
        .'the problem persists.');
    }

    page::get()->setHttpResponseCode(500)//Internal Server Error
        ->set('title', 'Error &laquo; '.config::get()->getConfig('app_name'))
        ->clearViews()//clear out any templates we were going to show to user.
        ->setMainView('main/app.php')
        ->addView('error-generic.php', 'content')
        ->renderViews();

    exit;

});


$config = config::get();

title::get()
    ->setSubtitleDisabled()//by default don't render the subtitle
    ->addCrumb($config->getConfig('app_name'));
if(utils::isPost()){
	validator::setForm($_POST);
	validator::autoRestoreForm(true);
}

//main template setup
page::get()->setMainView('main/app.php')
    ->set('render_header', true)
    ->set('render_footer', true)
    ->addView('read_messages.php', 'pre-http-header-fullpage')//read cookies
    ->addView(function(){
        //send header to user so they see something while page loading.
        if(ob_get_length() > 0){
            ob_implicit_flush(true);
            ob_flush();
            ob_implicit_flush(false);
        }
    }, 'post-body-header')
    ->addView('print_messages.php', 'print_messages')//prepare to print the messages
    ->addView(function($tpl){//wrap messages in a view so that we can disable it if needed.
        $tpl->renderViews('print_messages');
    }, 'pre-content')
    ->addView(function(){ ?>
        <div class="catchall padding-3-5"></div>
    <?php }, 'top_spacing')
    ->addView(function(){ ?>
        <div class="catchall padding-3-5"></div>
    <?php }, 'bottom_spacing')
    ->addView(function($tpl){
        $tpl->renderViews('top_spacing');
    }, 'pre-header')
    ->addView(function($tpl){
        $tpl->renderViews('bottom_spacing');
    }, 'content')
    ->addView(function($tpl){//wrap messages in a view so that we can disable it if needed.
        $tpl->renderViews('print_messages');
    }, 'pre-content')
    ->addView(function(){//add font library ?>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,700,600' rel='stylesheet' type='text/css'>
     	<style type="text/css">
     		@font-face {
			  font-family: 'Pacifico';
			  font-style: normal;
			  font-weight: 400;
			  src: local('Pacifico Regular'), local('Pacifico-Regular'), url('/assets/common/webfonts/yunJt0R8tCvMyj_V4xSjafesZW2xOQ-xsNqO47m55DA.woff') format('woff');
			}
     	</style>
     <?php }, 'before_body_end')
    ->addView('elements/top_nav.php', 'header-with-nav')
    ->setNoClear('header-with-nav');

db::connect(//connect to database
    $config->getConfig('db', 'host'),
    $config->getConfig('db', 'user'),
    $config->getConfig('db', 'pass'),
    $config->getConfig('db', 'name')
);

//get php's offset from UTC
$hours_offset = (date('Z') / 3600);
if($hours_offset >= 0){
    $hours_offset = '+'.$hours_offset;
}
//set database to use php's timezone
db::query('SET @@session.time_zone = "'.$hours_offset.':00"');

//set default access requirements
$acl = accessControl::get();

//by default, visitor must be a logged in user
$acl->requires('member', function(){
    messages::setErrorMessage('Please log in to view that page.');
    utils::redirectTo('/login');
});


//create navigation menu
sidebarV2::get('top_nav')
	->setMainView('/sidebar/main_menu_nav.php')
    ->addLink(
        sidebarV2::createLink('Home')
            ->setMainView('/sidebar/menu_item.php')
            ->setHref('/')
    )
    ->addLink(
        sidebarV2::createLink('Download')
			->setInner('Download <div class="notifications rose">Coming Soon</div>')
            ->setMainView('/sidebar/menu_item.php')
            ->setHref('/download')
    );

page::get()//add it to the top nav
	->addView(sidebarV2::get('top_nav'), 'nav')
	->addView(function(){
		//TODO: read .git/HEAD to get the modified time of current branch
		$time = date('Y-m-d H:i:s', filemtime('../.git/logs/refs/heads/master')); ?>
		Last update: <span class="timeago" title="<?php
		echo $time; ?> +0000"><?php echo $time; ?></span>
	<?php }, 'top_nav-left');

if(user::isUserLoggedIn()){

    $acl->grant('member');//allow user to see pages requiring login

    page::get()
	        ->addView('elements/account_nav.php', 'top_nav_extra_items');

    //get user's custom perms
    foreach(user::getUserLoggedIn()->getGrants() as $grant){
        if($grant->onOff === '1'){
            $acl->grant($grant->grant_name);
        }elseif($grant->onOff === '0'){
            $acl->deny($grant->grant_name);
        }
    }

	sidebarV2::get('top_nav')
	    ->addLink(
	        sidebarV2::createLink('Log Out')
	            ->setMainView('/sidebar/menu_item.php')
	            ->setHref('/logout')
				->setContainerClass('hideDesktop')
	    );
}else{

    $acl->grant('guest');//allow user to see pages requiring guest

        page::get()
	        ->addView(function(){
	        	?><a class="item">Welcome Guest!</a><?php
	        }, 'top_nav_extra_items');

	sidebarV2::get('top_nav')
	    ->addLink(
	        sidebarV2::createLink('Sign Up')
	            ->setMainView('/sidebar/menu_item.php')
	            ->setHref('/signup')
				->setContainerClass('hideDesktop')
	    )->addLink(
	        sidebarV2::createLink('Log In')
	            ->setMainView('/sidebar/menu_item.php')
	            ->setHref('/login')
				->setContainerClass('hideDesktop')
	    );
    page::get()->addView(function(){ ?>
    <div class="buttons_container hideTablet hideMobile">
        <a data-overlay-id="signup" data-max_width="420" class="btn btn-medium btn-white" href="/signup">SIGN UP FREE!</a>
        <a data-overlay-id="login" data-max_width="400" class="btn btn-medium btn-gray" href="/login">LOG IN</a>
    </div>
    <?php }, 'nav');
}

if(//grant webmaster priviledges (benchmarking, debugging, crons, etc)
    php_sapi_name() === 'cli'//command line exectuion
    || $_SERVER['REMOTE_ADDR'] === $_SERVER['SERVER_ADDR']//request came from same machine as server
    || (//visitor has admin password cookie (possibly remove in future for security)
            isset($_COOKIE['admin_key'])
            && config::hasSetting('admin_key')
            && config::getSetting('admin_key') == $_COOKIE['admin_key']
        )
){
    $acl->grant('webmaster');
}
