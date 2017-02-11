<?php namespace toolbox; ?>
<div id="<?php echo $widget_id; ?>"
	class="widget <?php echo $class; ?>"
	style="<?php if(isset($style)) echo $style; ?>">
    <div class="widget-header">
        <?php echo $title; ?>
        <div class="widget-header-controls">
    	    <form method="post" action="<?php
    	    echo $_SERVER['REQUEST_URI'];
    	    ?>"
    	        data-ajax_form="#<?php echo $widget_id; ?>"
    	        data-ajax_replace="true"
    	        data-show_loader="#<?php echo $widget_id; ?>.widget > .widget-content">
    	        <?php foreach($unique_ids as $key => $value){ ?>
    	        	<input type="hidden" name="<?php
    	        		echo utils::htmlEncode($value); ?>" value="<?php echo utils::htmlEncode($this->{$value}); ?>">
    	        <?php } ?>
                    <button class="btn btn-small btn-silver" type="submit"><i class="icon-arrows-ccw single"></i></button>
    	    </form>
        </div>
    </div>
    <div class="widget-content">
        <?php $this->renderViews('widget_content'); ?>
    </div>
<div class="catchall"></div>
</div>