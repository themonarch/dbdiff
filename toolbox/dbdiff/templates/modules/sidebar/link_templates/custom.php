<div class="item <?php if($this->active == $name){ echo "active"; } ?>">
	<a href="<?php echo $data['href']; ?>">
		<span class="title"><?php echo $name; ?></span>
		<?php if(isset($data['subtitle']) && $data['subtitle'] !== ''){ ?>
		      <span class="note"><?php echo $data['subtitle']; ?></span>
		<?php } ?>
	</a>
</div>