<?php namespace toolbox; ?>
<div class="form-element input-radio">
<div class="input-wrapper<?php echo $wrapper_class; ?>">

<?php $grids = floor(12/count($options)); ?>
		     <?php foreach($options as $opt_name => $opt_value){ ?>
<label class="grid-<?php echo $grids; ?>">
		<input
		     id="input-<?php echo $name; ?>"
		     type="radio"
		     name="<?php echo $name; ?>"
		     <?php if(isset($size)){ ?>
		     size="<?php echo $size; ?>"
		     <?php } ?>
		     <?php if(isset($autocomplete)){ ?>
		     autocomplete="<?php echo $autocomplete; ?>"
		     <?php } ?>
		     class="<?php echo $class; ?>"
	    	value="<?php echo $opt_value; ?>"
		     <?php if($disabled != false){ ?>
		     disabled="disabled"
		     <?php } ?> <?php if($value.'' === $opt_value.''){ echo 'checked="checked"'; } ?>>

<?php echo $opt_name; ?>
</label>

		     	         <?php } ?>
     <?php if(isset($error)){ ?>
     	<span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
  	<?php if(isset($note)){ ?>
     	<div class="note"><?php echo $note; ?></div>
     <?php } ?>
	 </div>
</div>