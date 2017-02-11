<?php namespace toolbox; ?>
    <?php messages::printMessages('change_password'); ?>
    <form class="form clearfix" method="post"
    action="/ajax/change_password" data-ajax_form="#change_password"
    data-show_loader="#change_password">
    <table class="steps">
        <tbody>
            <tr>
                <td class="first">Current Password:</td>
                <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('current_password')
                    ->setLabel(false)
                    ->setPlaceholder('Current Password')
                    ->render();
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 15px;"><div class="catchall-border"></div></td></tr>
            <tr>
            <tr>
                <td class="first">New Password:</td>
                <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('password')
                    ->setLabel(false)
                    ->setPlaceholder('New Password')
                    ->render();
                ?>
                </td>
            </tr>
            <tr>
                <td colspan="2"><div class="catchall spacer-1"></div></td>
            </tr>
            <tr>
                <td class="first">Confirm New Password:</td>
                <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('password2')
                    ->setLabel(false)
                    ->setPlaceholder('New Password Again')
                    ->render();
                ?>
                </td>
            </tr>
            <tr>
                <td class="first"></td>
                <td>
                    <br>
                    <input type="submit" class="btn btn-medium btn-blue" value="Change Password" name="submit">
                </td>
            </tr>
        </tbody>
    </table>

        <div class="catchall"></div>
    </form>