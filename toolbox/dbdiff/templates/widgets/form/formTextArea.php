<div class="form-element textarea">
<div class="input-wrapper<?php echo $wrapper_class; ?>">
<label for="input-<?php echo $name; ?>"> <?php echo $label; ?></label>
<textarea
     id="input-<?php echo $name; ?>"
     name="<?php echo $name; ?>"
     rows="<?php echo $rows; ?>"
     cols="<?php echo $columns; ?>"
     placeholder="<?php echo $placeholder; ?>"
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