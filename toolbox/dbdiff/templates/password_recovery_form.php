<?php namespace toolbox; ?>
<div class="section centered colored padded">
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2>Password Recovery</h2>
            </div>
            <div class="section-content">
                <?php
                    messages::printMessages('forgot_password');
                ?>
                <div class="clear-sidebar">
                	<div class="messages-container">
                		<div class="messages" style="display: block;">
                			<div class="message">
                				Submit the email address associated with your account and we will send you
                				an email containing instructions on how to reset your password.
                			</div>
                		</div>
                	</div>
                </div>
                <div class="grid-responsive-8 gridcenter" style="width: 100%;">
                    <form class="form clearfix" method="post" action="/ajax/password_recovery" data-ajax_form="#forgot_password > .section"
                    data-show_loader="#forgot_password">
                        <?php
                        form::textField()
                                ->setTypeText()
                                ->setName('email')
                                ->render();
                        ?>
                        <div style="text-align: right; margin: 15px 0px 0;">
                            <a style="float: left;" href="/" class="btn btn-medium">&laquo; Back to Login</a>
                            <input type="submit" class="btn btn-medium btn-blue" value="Send Recovery Email" name="submit">
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>