<?php namespace toolbox; ?>
<div class="sidebar-container <?php echo $class; ?>" id="user-sidebar">
	<div class="sidebar">
		<!-- <div class="logo_container"><a href="/monitor" class="logo">HeatSync</a></div> -->
		<?php
		$this->renderViews('top');
		?>
		<div <?php echo $custom_attributes; ?> class="menu">
			<?php if(!empty($links)) foreach($links as $id => $link){
				//if link id matches first active id
				/*if(isset(current($active)) && current($active) === $id){
					$link->set('active', true);
					next($active);
				}else{
					$link->set('active', false);
				}*/

				if($link->getID() === $this->getCurrentActive()){
					$link->setActive($this->getNextActive());
				}
				$link->renderViews();

			} ?>
			<?php
			$this->renderViews('post-links');
			?>
	    </div>

	</div>

<div class="background">

</div>

</div>
<?php
