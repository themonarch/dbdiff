<?php namespace toolbox; ?>
<div class="section centered" id="password_reset-container">
    <div class="contents">
        <div class="contents-inner">
            <div class="section-header">
                <h2 style="text-align: center;">Password Reset</h2>
            </div>
            <div class="section-content">

<div class="form_panel style1" id="password_reset">
    <form method="post" class="form clearfix">
        <?php
        messages::output('<b>Warning:</b> Once you submit your new password, some of the settings on your account will
                    be lost because of the way we encrypt sensitive data using your password as part of
                    your account\'s encryption key.', 'warning', 'style3'); ?>
        <div class="catchall spacer-3"></div>

    <?php messages::printMessages('reset_password'); ?>
            <table class="steps">
                <tbody>
                    <tr>
                        <td class="path">
                            <div class="path-content">1</div>
                            <div class="path-line"></div>
                        </td>
                        <td class="first">New Password:</td>
                        <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('password')
                    ->setLabel(false)
                    ->setPlaceholder('New Password')
                    ->render();
                ?><div class="note">Must be at least 6 characters long.</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="path"><div class="path-line"></div></td>
                        <td style="padding: 15px;" colspan="2">
                            <div class="catchall-border"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="path">
                            <div class="path-content">2</div>
                        </td>
                        <td class="first">Confirm New Password:</td>
                        <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('password2')
                    ->setLabel(false)
                    ->setPlaceholder('New Password')
                    ->render();
                ?></td>
                    </tr>
                    <tr><td class="path">
                        <td style="padding: 15px;" colspan="2">
                            <div class="catchall-border"></div>
                        </td></tr>
                    <tr>
                        <td class="path">
                        </td>
                        <td class="first"></td>
                        <td><input type="submit" name="submit" value="Apply New Password" class="btn btn-medium btn-blue"></td>
                    </tr>
                </tbody>
            </table>

                <div class="catchall"></div>
            </form>
        </div>
            </div>
        </div>
    </div>
</div>