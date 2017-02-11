<?php namespace toolbox;
if(!isset($value_display_name)){
    $value_display_name = $value;
}
?>
<div class="form-element input-choose" id="input-choose-<?php echo utils::toSlug($name); ?>">
<div class="input-wrapper <?php echo $wrapper_class; ?>">
<label for="input-<?php echo $name; ?>">
        <?php echo $label; ?></label>
        <input type="hidden"
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

        <input type="hidden"
             id="input-<?php echo $name; ?>"
             name="display_name-<?php echo $name; ?>"
             value="<?php if(isset($value_display_name)) echo utils::htmlEncode($value_display_name); ?>"
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

                    <div class="picker">
                        <div class="grid-9">
                            <div class="picker-value-container">
                                <?php if($value !== ''){ ?>
                                <div class="picker-value" title="<?php echo utils::htmlEncode($value_display_name); ?>"><?php
                                    $this->renderViews('value-left');
                                    echo $value_display_name;
                                ?></div>
                                <?php }else{ ?>
                                <div class="picker-placeholder">Please make a selection</div>
                                <?php } ?>
                                <?php if($remove){ ?>
                                    <button title="Remove this field"
                                    	class="picker-icon picker-remove" name="remove" value="<?php
                                        echo $name; ?>" type="submit"><i class="icon-cancel-squared"></i></button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="grid-3">
                        	<?php if(isset($ajax_url)){ ?>
                            <a href="<?php echo $ajax_url; ?>"
                            class="btn picker-action btn-blue"
                            data-overlay-id="<?php echo $overlay_id; ?>">Choose</a>
                            <?php }else{ ?>
                            <button type="submit" name="submit" class="btn picker-action btn-medium btn-blue"
                            	value="<?php echo $name; ?>">Choose</button>
                            <?php } ?>
                        </div>
                        <div class="catchall"></div>
                    </div>



     <?php if(isset($error)){ ?>
        <span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
     <?php if(isset($note)){ ?>
     <div class="note"><?php echo $note; ?></div>
     <?php } ?>
     </div>
</div>