<?php namespace toolbox; ?>

<div class="form_panel style1" id="password_reset">
            <form method="post" class="form clearfix">
    <?php messages::printMessages('reset_password'); ?>
            <table class="steps">
                <tbody>
                    <tr>
                        <td class="path hideMobile">
                            <div class="path-content">1</div>
                            <div class="path-line"></div>
                        </td>
                        <td class="first">Account:</td>
                        <td><?php
                form::textField()
                    ->setTypeText()
                    ->setName('email')
                    ->setLabel(false)
                    ->setDisabled()
                    ->setPlaceholder('Email Address')
                    ->setValue($email)
                    ->render();
                ?><div class="note"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="path hideMobile"><div class="path-line"></div></td>
                        <td style="padding: 15px;" colspan="2">
                            <div class="catchall-border"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="path hideMobile">
                            <div class="path-content">2</div>
                            <div class="path-line"></div>
                        </td>
                        <td class="first">Create a Password:</td>
                        <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('password')
                    ->setLabel(false)
					->setNote('Must be at least 6 characters long.')
                    ->setPlaceholder('Password')
                    ->render();
                ?><div class="note"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="path hideMobile"><div class="path-line"></div></td>
                        <td style="padding: 15px;" colspan="2">
                            <div class="catchall-border"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="path hideMobile">
                            <div class="path-content">3</div>
                            <div class="path-line"></div>
                        </td>
                        <td class="first">Confirm Password:</td>
                        <td><?php
                form::textField()
                    ->setTypePassword()
                    ->setName('password2')
                    ->setLabel(false)
                    ->setPlaceholder('Confirm Password')
                    ->render();
                ?></td>
                    </tr>
                    <tr>
                        <td class="path hideMobile"><div class="path-line"></div></td>
                        <td style="padding: 15px;" colspan="2">
                            <div class="catchall-border"></div>
                        </td>
                    </tr>
                    <tr>
                       <td class="path hideMobile">
                            <div class="path-content">
					<i class="icon-ok"></i></div>
                        </td>
                        <td class="first"></td>
                        <td style="text-align: left;"><input type="submit" name="submit" value="Apply Password" class="btn btn-medium btn-blue"></td>
                    </tr>
                </tbody>
            </table>

                <div class="catchall"></div>
            </form>
        </div>
