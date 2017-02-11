<?php namespace toolbox; ?>
<div class="form clearfix">
    <?php messages::printMessages('change_email'); ?>
	<table class="steps">
		<tbody>
			<tr>
				<td class="first">Pending Email Address:</td>
				<td><?php
                form::textField()
                    -> setTypeText()
                    -> setName('new_email')
                    -> setDisabled()
                    -> setValue($pending_email)
                    -> setLabel(false)
                    -> setPlaceholder('Email Address')
                    -> render();
				?></td>
			</tr>
			<tr>
				<td class="first"></td>
				<td>
    				<div class="note">
    					<b>Note:</b> Your email address and login will change to your new email
    					<b>only after you click the activation link</b> sent to your new email address.
    				</div>
    				<br>
			        <form class="form clearfix" style="display: inline-block;" method="post"
                        action="/ajax/change_email" data-ajax_form="#change_email_form"
                        data-show_loader="#change_email_form">
                        <input type="hidden" name="action" value="cancel">
						<input type="submit" class="btn btn-medium btn-gray" value="Cancel Email Change" name="submit">
					</form>
			        <form class="form clearfix" style="display: inline-block;" method="post"
                        action="/ajax/change_email" data-ajax_form="#change_email_form"
                        data-show_loader="#change_email_form">
                        <input type="hidden" name="action" value="resend">
                        <input type="submit" class="btn btn-medium btn-blue" value="Resend Validation Link" name="action">
					</form>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="catchall"></div>
</div>
