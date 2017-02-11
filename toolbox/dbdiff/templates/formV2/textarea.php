<?php namespace toolbox; ?>
<div class="form-element input-textarea">
<div class="input-wrapper <?php echo $wrapper_class; ?>">
<label for="input-<?php echo $name; ?>">
        <?php echo $label; ?></label>
        <textarea
             id="input-<?php echo $name; ?>"
             name="<?php echo $name; ?>"
             <?php if(isset($maxlength)){ ?>
             <?php } ?>
             <?php if(isset($placeholder)){ ?>
             placeholder="<?php echo $placeholder; ?>"
             <?php } ?>
             <?php if(isset($autocomplete)){ ?>
             autocomplete="<?php echo $autocomplete; ?>"
             <?php } ?>
             class="<?php echo $class; ?>"
             <?php if($disabled != false){ ?>
             disabled="disabled"
             <?php } ?>><?php echo $value; ?></textarea>
     <?php if(isset($error)){ ?>
        <span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
     <?php if(isset($note)){ ?>
     <div class="note"><?php echo $note; ?></div>
     <?php } ?>
     </div>
</div>