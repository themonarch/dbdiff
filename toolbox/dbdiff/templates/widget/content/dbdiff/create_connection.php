<?php
namespace toolbox;
messages::readMessages($index);
?>
<form data-ajax_form="#<?php echo $widget_id; ?>"
    data-show_loader="#<?php echo $widget_id; ?>"
    data-ajax_replace="true"
    class="form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

<div class="catchall"></div>
<?php messages::printMessages('quick_connect-'.$index); ?>

<div class="catchall spacer-1"></div>
<?php
	page::create()
		->setMainView('generic_layout/table-form-horizontal-v2.php')
		->set('sidelabels', false)
		->set('show_steps', false)
		->setArray('rows', 'Host',
			formV2::textField()
				->setLabel('Host')
	            ->setTypeText()
                ->setDisabled(page::get()->demo)
	            ->setName('Host['.$index.']')
				->addView(function(){ ?>
					<div class="catchall spacer-1"></div>
				<?php })
		)
		->setArray('rows', 'User',
			formV2::textField()
				->setLabel('User')
	            ->setTypeText()
                ->setDisabled(page::get()->demo)
	            ->setName('User['.$index.']')
				->addView(function(){ ?>
					<div class="catchall spacer-1"></div>
				<?php })
		)
		->setArray('rows', 'Password',
			formV2::textField()
				->setLabel('Password')
	            ->setTypePassword()
                ->setDisabled(page::get()->demo)
	            ->setName('Password['.$index.']')
				->setNote('Passwords are encrypted with a master key stored <span style="font-weight: 600;">only</span> in your browser.')
				->addView(function(){ ?>
					<div class="catchall spacer-1"></div>
				<?php })
		)
		->setArray('rows', 'Port',
			formV2::textField()
				->setLabel('Port')
                ->setDisabled(page::get()->demo)
	            ->setTypeText()
	            ->setName('Port['.$index.']')
				->setValue('3306')
                ->addView(function(){ ?>
                    <div class="catchall spacer-1"></div>
                <?php })
        )->setArray('rows', 'Database',
			formV2::choosefield()
				->setLabel('Database')
                ->setDisabled(page::get()->demo)
                ->setName('Database['.$index.']')
		)->renderViews();


?>


</form>
<div class="catchall"></div>
<?php
