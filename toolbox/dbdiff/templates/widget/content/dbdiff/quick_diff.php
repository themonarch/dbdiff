<?php namespace toolbox; ?>
<div id="quick_connect_form"
	data-dynamic_form=""
	data-action=""
    data-ajax_replace="true"
    data-output_destination="#<?php echo $widget_id; ?>"
    data-show_loader="#quick_connect_form">
<?php page::get()->renderViews('quick_diff-header'); ?>

<div style="position: relative;">

<div class="grid-6">
	<div class="form_panel padding" style="padding-top: 0px; padding-bottom: 0px;">
				<div class="header-line style1">
		    <div class="inner">Development</div>
		    <div class="gradient-line"></div>
		</div>
	</div>
</div>

<div class="grid-6">
	<div class="form_panel padding" style="padding-top: 0px; padding-bottom: 0px;">
				<div class="header-line style1">
		    <div class="inner">Production</div>
		    <div class="gradient-line"></div>
		</div>
	</div>
</div>


<div class="catchall"></div>
<div style="position: relative;">

	<div class="grid-6">
<div class="form_panel padding" style="padding-top: 0px; padding-bottom: 0px;">
	<?php page::get()->renderviews('connection-0'); ?>
	</div>

</div>
	<div class="header-line vertical style1" style="left: 50%;">
	    <div class="inner">Vs.</div>
	    <div class="gradient-line"></div>
	</div>

	<div class="grid-6">

<div class="form_panel padding"
    style="padding-top: 0px; padding-bottom: 0px;">

	<?php page::get()->renderviews('connection-1'); ?>
	</div>
    <div class="catchall"></div>
    </div>

<div class="catchall"></div>
</div>

</div>













<div class="catchall spacer-1"></div>
<div class="datatable ">


<div class="datatable-info datatable-section">
	<div style="max-width: 400px; margin: 0 auto;">
 	<input type="hidden" name="widget_unique_id" value="<?php echo $widget_unique_id; ?>">
        <input class="btn btn-silver  btn-medium btn-3d btn-full_width"
            name="submit" value="Compare" data-dynamic_form_submit="" type="submit">
</div>
</div>
	    	<div class="catchall"></div>
</div>


</div>