<?php namespace toolbox; ?>
<div class="form-element input-checkbox">
    <div class="input-wrapper <?php echo $wrapper_class; ?>">

<label for="input-<?php echo $name; ?>">
        <?php echo $label; ?></label>
        <select <?php $this->renderViews('custom_attributes'); ?> class="<?php echo $class; ?>" name="<?php echo $name; ?>">
            <?php
            if(isset($options))
            foreach($options as $array){
                if(is_array(current($array))){
                    extract(current($array));
                }else{
                    $title = current($array);
                }
            ?>
               <option value="<?php echo key($array); ?>" <?php
                    if( isset($value) && ($value == key($array)) ) echo 'selected="selected"'; ?> <?php
                    if( isset($attributes) ) echo $attributes; ?>>
                <?php echo $title; ?></option>
            <?php } ?>
        </select>
     <?php if(isset($error)){ ?>
        <span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
    </div>

</div>
