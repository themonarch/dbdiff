<?php namespace toolbox; ?>
<div data-tabs="" class="form-element input-checkbox">
    <div class="input-wrapper <?php echo $wrapper_class; ?>">
        <?php if(isset($label)){ ?><label for="input-description"><?php echo $label; ?></label><?php } ?>
		<div class="switches style2">
			<div data-tabs-container="" class="container">
                <?php
                foreach($options as $key => $title){ ?>
                   <span class="switch <?php if($value == $key) echo 'active'; ?>" data-tab="">
                    <input type="radio" <?php if($value == $key){ ?>checked="checked"<?php }
                    ?> name="<?php echo $name; ?>" value="<?php echo $key; ?>">
                    <?php echo $title; ?></span>
                <?php } ?>
			</div>
		</div>

     <?php if(isset($error)){ ?>
        <span class="input-error"><?php echo $error; ?></span>
     <?php } ?>
	</div>
<?php $this->renderViews('post-element'); ?>
</div>
