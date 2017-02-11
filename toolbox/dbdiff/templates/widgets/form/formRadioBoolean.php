<?php namespace toolbox; ?>
<div class="form-element input-text">
<div class="input-wrapper">
<label for="input-<?php echo $name; ?>"><?php echo $label; ?></label>
<div class="switches style1 radio">
    <div class="container">
        <a class="switch <?php
            if(!$value){ echo 'active'; } ?>">No <input type="radio" value="0" name="<?php echo $name; ?>" <?php
            if(!$value){ echo 'checked="checked"'; } ?>></a>
        <a class="switch <?php
            if($value){ echo 'active'; } ?>">Yes <input type="radio" value="1" name="<?php echo $name; ?>" <?php
            if($value){ echo 'checked="checked"'; } ?>></a>
    </div>
</div>
</div>
</div>