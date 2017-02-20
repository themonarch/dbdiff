<?php
namespace toolbox;
class account_controller {

    static function setup(){

    }

    function __construct(){

        $fields = array(
                    'first_name',
                    'last_name',
                    'address',
                    'country',
                    'region',
                    'city',
                    'postal_code'
                );

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            validator::setForm($_POST);
            foreach ($fields as $value) {
                validator::validate($value, 'general');
            }
            if(!validator::isValid()){
                form::storeValues($_POST);
                messages::setErrorMessage('There was an error with your submission.');
            }else{
                foreach ($fields as $value) {
                    user::getUserLoggedIn()->setCustomValue($value, $_POST[$value]);
                }
                messages::setSuccessMessage('Your contact details have been updated.');
            }

        }

        //menu::get('top_nav')
            //->setActive('My Account');

        sidebar::get('account')
            ->setActive('Update Contact Info');
            title::get()->addCrumb('Update Contact');
            page::get()
                ->set('subtitle', false)
                ->addView(function(){

                   sidebar::get('account')->render();
                }, 'pre-pre-content')
                ->addView(function($tpl){ ?>
            <div class="clear-sidebar">
                <div class="widget">
                    <div class="widget-header center">
                        Contact Information
                    </div>
                    <div class="widget-content">
        <div class="form_panel" id="email_validation_panel">
            <form method="post">
            <div class="form clearfix">
            <table class="steps">
                <tbody>
                    <tr>
                        <td class="first">First Name:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('first_name')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('first_name', false))
                        ->setPlaceholder('First Name:')
                        ->render();
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="catchall spacer-1"></div></td>
                    </tr>
                    <tr>
                        <td class="first">Last Name:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('last_name')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('last_name', false))
                        ->setPlaceholder('Last Name:')
                        ->render();
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="catchall spacer-1"></div></td>
                    </tr>
                    <tr>
                        <td class="first">Address:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('address')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('address', false))
                        ->setPlaceholder('Address:')
                        ->render();
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="catchall spacer-1"></div></td>
                    </tr>
                    <tr>
                        <td class="first">Country:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('country')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('country', false))
                        ->setPlaceholder('Country:')
                        ->render();
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="catchall spacer-1"></div></td>
                    </tr>
                    <tr>
                        <td class="first">State / Region:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('region')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('region', false))
                        ->setPlaceholder('State / Region:')
                        ->render();
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="catchall spacer-1"></div></td>
                    </tr>
                    <tr>
                        <td class="first">City:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('city')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('city', false))
                        ->setPlaceholder('City:')
                        ->render();
                        ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><div class="catchall spacer-1"></div></td>
                    </tr>
                    <tr>
                        <td class="first">Postal Code:</td>
                        <td><?php
                form::textField()
                        ->setTypeText()
                        ->setName('postal_code')
                        ->setLabel(false)
                        ->setValue(user::getUserLoggedIn()->getCustomValue('postal_code', false))
                        ->setPlaceholder('Postal Code:')
                        ->render();
                        ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="first"></td>
                        <td>
                        <br>
                        <input type="submit" name="submit" value="Save Contact Details" class="btn btn-medium btn-blue">
                        </td>
                    </tr>
                </tbody>
            </table>

                <div class="catchall"></div>
            </div>
            </form>
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
			->addLink('Update Contact Info', '', '/account',
			'default.php',
			array('content-left' => '<i class="icon-list-alt"></i>'));
    }

}
