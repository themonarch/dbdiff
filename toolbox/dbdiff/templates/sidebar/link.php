<?php
namespace toolbox;
$tag = 'div';
if(isset($href)){
    $tag = 'a';
}
?>
<div <?php echo $container_attributes; ?> class="item-wrapper <?php
if($active === true){
	echo 'active';
}
?>">
	<div class="item">
		<<?php echo $tag; ?> <?php echo $link_attributes; ?>
			<?php if(isset($href)){ ?> href="<?php echo $href; ?>" <?php } ?> class="item-main"> <span class="item-title"> <span class="text-left"></span><!--
			--><span class="content-left"></span> <?php
			echo $inner;
			?> </span>
		</<?php echo $tag; ?>>
		<?php
		if(isset($links))
		foreach($links as $id => $link){
			if($link->getID() === $this->getCurrentActive()){
				$link->setActive($this->getNextActive());
			}

			$link->renderViews();
		} ?>
	</div>
</div>
<?php
