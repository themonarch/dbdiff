<div class="form-element input-text">
<div class="input-wrapper <?php echo $wrapper_class; ?>">
<label for="input-<?php echo $name; ?>">
        <?php echo $label; ?></label>
        <?php if($type !== 'blank'){ ?>

        <div style="<?php echo $input_row_style; ?>">
        <div class="<?php echo $input_wrapper_class; ?>">
        <input style="<?php if(isset($max_width)){ ?>
             max-width: <?php echo $max_width; ?>px;
             <?php } ?>" type="<?php echo $type; ?>"
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
             /></div><?php } ?><?php $this->renderViews('post-input_wrapper'); ?></div>
             <div class="catchall"></div>
     <?php if(isset($error)){ ?>
        <span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
     <?php if(isset($note)){ ?>
     <div class="note"><?php echo $note; ?></div>
     <?php } ?>
     </div>
</div>