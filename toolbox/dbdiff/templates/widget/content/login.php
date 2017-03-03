<?php
namespace toolbox;
$this -> renderViews('pre-content'); ?>
<form class="form clearfix" method="post" action="/ajax/login" data-ajax_form="#login_form > .section"
	data-show_loader="#login_form">
		<?php
		messages::printMessages();
		messages::printMessages('loginForm');


        form::textField() -> setTypeText() -> setName('email') -> render();
        ?>
        <div class="catchall spacer"></div>
        <?php
        form::textField() -> setTypePassword() -> setName('password') -> render();
		?>
		<div style="text-align: right; margin: 15px 0px 0;">
			<a style="float: left;" href="/login/forgot" class="btn btn-medium">Forgot password?</a>
			<input type="submit" class="btn btn-medium btn-blue" value="Login" name="submit">
		</div>
        <div class="catchall"></div>
        <div class="form_link">
            Don't have an account? <a class="style1" href="/signup"> Sign up here. </a>
        </div>
		<?php
            $this -> renderViews('post_form');
		?>
</form>
