<div id="<?php echo $widget_id; ?>"
    class="form_panel style5 <?php echo $class; ?>"
    style="<?php if(isset($style)) echo $style; ?>">
        <?php $this->renderViews('widget_content'); ?>
<div class="catchall"></div>
</div>