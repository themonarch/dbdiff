<div id="<?php echo $widget_id; ?>"
	class="widget <?php echo $class; ?>"
	style="<?php if(isset($style)) echo $style; ?>">
    <div class="widget-header">
    	<?php $this->renderViews('header'); ?>
        <?php echo $title; ?>
    </div>
    <div class="widget-content">
        <?php $this->renderViews('widget_content'); ?>
    </div>
<div class="catchall"></div>
</div>