<?php namespace toolbox; ?>
<div class="form-element input-select">
<div class="input-wrapper<?php echo $wrapper_class; ?>">
<label for="input-<?php echo $name; ?>"><?php echo $label; ?></label>
		<select
		     id="input-<?php echo $name; ?>"
		     name="<?php echo $name; ?>"
		     <?php if(isset($size)){ ?>
		     size="<?php echo $size; ?>"
		     <?php } ?>
		     <?php if(isset($autocomplete)){ ?>
		     autocomplete="<?php echo $autocomplete; ?>"
		     <?php } ?>
		     class="<?php echo $class; ?>"
		     <?php if($disabled != false){ ?>
		     disabled="disabled"
		     <?php } ?>>


		     <?php foreach($options as $opt_value => $name){ ?>
                <option <?php if($value.'' === $opt_value.''){ echo 'selected="selected"'; } ?> value="<?php echo $opt_value; ?>"><?php echo $name; ?></option>
	         <?php } ?>



		     </select>
     <?php if(isset($error)){ ?>
     	<span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
  	<?php if(isset($note)){ ?>
     	<div class="note"><?php echo $note; ?></div>
     <?php } ?>
	 </div>
</div>