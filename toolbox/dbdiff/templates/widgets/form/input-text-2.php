<div class="form-element input-text style2">
<label for="input-<?php echo $name; ?>"><?php echo $label; ?></label>
<input type="<?php echo $type; ?>"
     id="input-<?php echo $name; ?>"
     name="<?php echo $name; ?>"
     value="<?php echo $value; ?>"
     <?php if(isset($maxlength)){ ?>
 	maxlength="<?php echo $maxlength; ?>"
     <?php } ?>
     <?php if(isset($placeholder)){ ?>
     placeholder="<?php echo $placeholder; ?>"
     <?php } ?>
     <?php if(isset($size)){ ?>
     size="<?php echo $size; ?>"
     <?php } ?>
     <?php if(isset($autocomplete)){ ?>
     autocomplete="<?php echo $autocomplete; ?>"
     <?php } ?>
     class="<?php echo $class; ?>"
     <?php if($disabled != false){ ?>
     disabled="disabled"
     <?php } ?>
     />
</div>