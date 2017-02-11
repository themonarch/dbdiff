<?php namespace toolbox; ?>
<div class="form clearfix">
    <?php messages::printMessages('change_email'); ?>
	<form class="form clearfix" method="post"
	action="/ajax/change_email" data-ajax_form="#change_email_form"
	data-show_loader="#change_email_form">
		<table class="steps">
			<tbody>
				<tr>
					<td class="first">New Email Address:</td>
					<td><?php form::textField()
					              -> setTypeText()
					              -> setName('email')
					              -> setLabel(false)
					              -> setPlaceholder('Email Address')
					              -> render(); ?>
	                  <div class="note">
					<b>Note:</b> This will be your <b>new login once you click the activation link</b> sent to the
					email address entered above.</div></td>
				</tr>
				<tr>
					<td colspan="2" style="padding: 15px;"><div class="catchall-border"></div></td>
				</tr>
				<tr>
					<td class="first">Confirm <?php echo config::get()->getConfig('app_name'); ?> Password:</td>
					<td><?php
                    form::textField()
                        -> setTypePassword()
                        -> setName('password')
                        -> setLabel(false)
                        -> setPlaceholder('Your '.config::get()->getConfig('app_name').' Password')
                        -> render();
					?></td>
				</tr>
				<tr>
					<td class="first"></td>
					<td>
					<br>
					<input type="submit" class="btn btn-medium btn-blue" value="Send Validation Link" name="submit">
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<div class="catchall"></div>
</div>