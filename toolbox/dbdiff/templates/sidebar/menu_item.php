<?php
namespace toolbox;
?>
<div <?php echo $container_attributes; ?> class="nav-button-container <?php echo $container_class; ?>">
		<a <?php echo $link_attributes; ?>
			href="<?php echo $href; ?>" class="nav-button <?php
			if(isset($links)){ ?> nav-button-dropdown <?php }
			?> <?php
if($active === true){
    echo 'nav-button-current';
} ?>"> <span class="item-title"> <span class="text-left"></span><!--
			--><span class="content-left"></span>
			<?php
			echo $inner;
			?> <?php
            if(isset($links)){ ?><i class="icon-down-micro control"></i><?php } ?></span>
		</a>
		<?php if(isset($links)){ ?>
        <div class="nav-button-dropdown-contents">
        <div class="nav-button-dropdown-contents-inner">
		<?php
		foreach($links as $id => $link){
			if($link->getID() === $this->getCurrentActive()){
				$link->setActive($this->getNextActive());
			}

			$link->renderViews();
		} ?>
        </div>
        </div>
        <?php } ?>
</div>
<?php
