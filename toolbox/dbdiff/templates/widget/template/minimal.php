<div id="<?php echo $widget_id; ?>" class="<?php echo $class; ?>" style="position: relative;">
<?php $this->renderViews('widget_content'); ?>
<div class="catchall"></div><?php //must be inside incase of ajax form (otherwise it all keep appending on each ajax request); ?>
</div>