<?php namespace toolbox; ?>
<!-- <style type="text/css" scoped>
    #wrapper{
        padding-left: 325px;
    }

</style> -->

<div class="sidebar-container item" id="user-sidebar">
    <?php if(isset($this->title)){ ?>
    <div class="sidebar-title"><?php echo $this->title; ?></div>
    <?php } ?>

	<div class="sidebar <?php echo $this->class; ?>">
		<!-- <div class="logo_container"><a href="/monitor" class="logo">HeatSync</a></div> -->

		<div class="menu">
			<?php foreach($this->links as $name => $data){
				$custom_data = $data['custom_data'];
				require toolbox::getPathApp().'/templates/modules/sidebar/link_templates/'.$data['template'];
			} ?>
	    </div>

	</div>


<div class="background">

</div>

</div>
<?php
	/*page::get()->addView(function(){ ?>
			<div class="sidebar_footer">&copy; <?php echo date('Y', time()); ?> HeatSync.com
			<br>All rights reserved</div>
	<?php }, 'footer-pre-contents');*/