<?php
namespace toolbox;
class account_denied_internal {

    static function setup(){
        router::get()
            ->setMaxParams(false)
            ->setMinParams(false);
        accessControl::get()
            ->clearRequirements();
        title::get()
            ->addCrumb('Access Denied');
        messages::readMessages('account_denied');
    }

    function __construct(){

		page::get()
            ->clearViews()
            ->set('header', 'Access Denied!')
            ->setMainView('main/app.php')
            ->setHttpResponseCode('403')
            ->addView(function($tpl){ ?>
                <div class="section centered">
                    <div class="contents">
                        <div class="contents-inner">
                            <div class="section-header">
                                <h2 style="text-align: center;"><?php if(isset($tpl->title)) echo $tpl->title;
                                    else echo 'Account Pending Approval!'; ?></h2>
                            </div>
                            <div class="section-content">
                                <?php messages::printMessages('account_denied'); ?>
                            <div style="text-align: center;">
                                <a class="btn btn-link" href="/logout">Log Out</a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }, 'content')
            ->renderViews();
    }

	static function passThru(){

	}
}
