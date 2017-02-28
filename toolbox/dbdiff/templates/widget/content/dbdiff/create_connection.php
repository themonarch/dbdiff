<?php
namespace toolbox;
messages::readMessages($index);
?>
<form data-ajax_form="#<?php echo $widget_id; ?>"
    data-show_loader="#<?php echo $widget_id; ?>"
    data-ajax_replace="true"
    class="form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

<div class="catchall"></div>
<?php messages::printMessages($index); ?>

<div class="catchall spacer-1"></div>
<?php if(page::get()->demo){ ?>
	<input type="hidden" name="demo" value="true">
<?php } ?>
<input style="display:none" type="text" name="fakeusernameremembered"/>
<input style="display:none" type="password" name="fakepasswordremembered"/>
<?php
	page::create()
		->setMainView('generic_layout/table-form-horizontal-v2.php')
		->set('sidelabels', false)
		->set('show_steps', false)
		->setArray('rows', 'Host',
			formV2::textField()
				->setLabel('Host')
				->setPlaceholder('Ex: example.com')
				->setNote('Need to connect to your localhost? It\'s easy,
					<a data-ajax_overlay="" href="/learn_localhost" data-overlay-id="learn_localhost">learn how!</a>')
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
				->setPlaceholder('Ex: Root')
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
				->setNote('We\'ll encrypt your passwords with a randomly generated master key stored <span style="font-weight: 600;">only</span> in your browser.')
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
                ->set('btn_color', 'silver')
                ->setDisabled(page::get()->demo)
                ->setName('Database['.$index.']')
		)->renderViews();


?>


</form>
<div class="catchall"></div>
<?php
