<?php namespace toolbox;
if(isset($option_current) && isset($options[$option_current])){
    $current_name = $options[$option_current]['name'];
}else{
    $option_current = null;
    $current_name = 'Choose an Option';
}
$default_option = array(
	'href' => '#',
	'name' => '[name]',
	'attr' => ''
);


?>
<div class="dropdown style1" data-dropdown="true">
    <div class="controls">
        <span> <?php echo $current_name; ?> <i class="icon-down-open open"></i></span>
    </div>
    <div class="dropdown-contents" data-dropdown-contents="true">
        <?php
            foreach($options as $key => $option){ $option = array_merge($default_option, $option ); ?>
                <a href="<?php
                    echo $option['href'];
                ?>" class="dropdown-select <?php
                    if($key === $option_current) echo 'active';
                ?>" <?php
                    echo $option['attr'];
                ?>><?php
                    echo $option['name'];
                ?></a>
        <?php } ?>
    </div>
</div>
