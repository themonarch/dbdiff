<div class="form-element input-checkbox">
<div class="input-wrapper <?php echo $wrapper_class; ?>">
<label for="input-<?php echo $name.'-'.$value; ?>">
<?php $this->renderViews('prelabel'); ?>
        <input type="checkbox"
             id="input-<?php echo $name.'-'.$value; ?>"
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
             <?php if($checked){ ?>
             checked="checked"
             <?php } ?>
             />
        <?php echo $label; ?></label>
     <?php if(isset($error)){ ?>
        <span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
     <?php if(isset($note)){ ?>
     <div class="note"><?php echo $note; ?></div>
     <?php } ?>
     </div>
</div>