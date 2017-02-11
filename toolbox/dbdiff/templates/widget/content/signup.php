<?php namespace toolbox; ?>
<?php if(user::ipHasAccount()){ ?>
<script src='https://www.google.com/recaptcha/api.js'></script>
<?php } ?>
<div class="section centered colored padded">
    <?php
        $this -> renderViews('pre-content');
    ?>
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2>Sign Up</h2>
            </div>
            <div class="section-content clearfix">
                <?php
                    messages::printMessages('signup');
                ?>
                <div class="grid-responsive-8 grid-center" style="width: 100%;">
                    <?php if(!db::isConnected('default')){ ?>
                        <div class="messages-container">
                            <div class="messages messages-warning">
                                We are currently undergoing maintenance, please come back in a little bit.
                            </div>
                        </div>
                    <?php }else{ ?>
                    <form class="form clearfix" method="post"
                    action="/ajax/signup" data-ajax_form="#signup_form > .section"
                    data-show_loader="#signup_form">
                        <?php
                        form::textField()
                                ->setTypeText()
                                ->setName('first_name')
                                ->setLabel('First Name')
                                ->render(); ?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypeText()
                                ->setName('last_name')
                                ->setLabel('Last Name')
                                ->render(); ?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypeText()
                                ->setName('email')
                                ->setPlaceholder('')
                                ->render(); ?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypePassword()
                                ->setName('password')
                                ->setPlaceholder('')
                                ->render();?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypePassword()
                                ->setName('password2')
                                ->setLabel('Retype Password')
                                ->setPlaceholder('')
                                ->render();?>
                            <div class="catchall spacer"></div>
                        <?php
                        form::textField()
                                ->setTypeHidden()
                                ->setName('website')
                                ->render();?>
                            <div class="catchall spacer"></div>
                        <?php

                        if(user::ipHasAccount()){ ?>
                        <label>Check the Box to Prove You're Not a Robot: </label>
                        <div style="margin: 0px auto; text-align: center; display: table;">
                            <div class="g-recaptcha" data-sitekey="<?php echo config::getSetting('reCAPTCHA-site'); ?>"></div>
                        </div>
                        <?php } ?>
                        <div style="text-align: right; margin: 15px 0px 0;">
                            <span class="" style="float: left; padding: 10px 0px;">Already have an account?
                                <a class="style1" href="/">
                                Log in here.
                                </a>
                            </span>
                            <input type="submit" class="btn btn-medium btn-blue" value="Create Account" name="submit">
                        </div>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
