<?php
namespace toolbox;
class account_controller {

    static function setup(){

    }

    function __construct(){


        //menu::get('top_nav')
            //->setActive('My Account');

        sidebar::get('account')
            ->setActive('Change Email / Password');

            page::get()
                ->set('subtitle', false)
                ->set('pending_email', user::getUserLoggedIn()->getCustomValue('email_change'))
                ->addView(function(){

                   sidebar::get('account')->render();
                }, 'pre-pre-content')
                ->addView(function($tpl){ ?>
<div class="clear-sidebar">
	<div class="catchall spacer-3"></div>
	<div class="widget">
		<div class="widget-header center">
			Email Validation Status
		</div>
		<div class="widget-content">
			<div class="form_panel" id="email_validation_panel">
				<div class="form clearfix">
					<table class="steps">
						<tbody>
							<tr>
								<td class="first">Current Email Address:</td>
								<td><?php
form::textField()
->setTypeText()
->setName('current_email')
->setLabel(false)
->setDisabled()
->setValue(user::getUserLoggedIn()->getEmail())
->setPlaceholder('Email Address')
->render();
								?></td>
							</tr>
							<tr>
								<td colspan="2" style="padding: 15px;"><div class="catchall-border"></div></td>
							</tr>
							<tr>
								<td class="first">Email Status:</td>
								<td><?php if(user::getUserLoggedIn()->hasValidatedEmail()){
								?>
								<span style="color: #1d9a0e; font-weight: bold;"><i class="icon-ok"></i> Verified</span><?php }else{
								?>
								<span style="color: #FFAC16; font-weight: bold;">Not Verified</span>
								<div class="note">
									To verify your account, click the validation link we sent to your email.
									If haven't recieved one, simply click the button below to send it again.
								</div>
								<br>
								<?php $tpl->render('elements/resend_email_validation.php'); ?>
								<?php } ?></td>
							</tr>
						</tbody>
					</table>

					<div class="catchall"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="catchall spacer-3"></div>
	<div class="widget">
		<div class="widget-header center">
			Change Email Address / Login
		</div>
		<div class="widget-content">
			<div class="form_panel" id="change_email_form">
				<?php
                if($tpl->pending_email !== null){
                    $tpl->render('elements/change_email_pending_form.php');
                }else{
                    $tpl->render('elements/change_email_form.php');
                }
				?>
			</div>
		</div>
	</div>
	<div class="catchall spacer-3"></div>
	<div class="widget">
		<div class="widget-header center">
			Change Password
		</div>
		<div class="widget-content">
			<div class="form_panel" id="change_password">
				<?php $tpl->render('elements/change_password.php'); ?>
			</div>
		</div>
	</div>

</div>

            <?php }, 'content-narrow');


    }

    static function passThru(){

        title::get()
            ->setSubtitleDisabled()
            ->addCrumb('Manage Account');

        sidebar::get('account')
            ->addLink('Change Email / Password', '', '/account',
                'default.php',
                array('content-left' => '<i class="icon-list-alt"></i>'))
            ->addLink('Update Contact Info', '', '/account/update_contact_info',
                'default.php',
                array('content-left' => '<i class="icon-list-alt"></i>'));
    }

}
