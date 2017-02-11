<?php namespace toolbox; ?>
<div class="notice-container <?php if(isset($container_style)) echo $container_style; ?>">
<div class="notice">
    <img class="icon" src="<?php echo config::get()->getConfig('cdn_url'); ?>/assets/<?php
            echo config::get()->getConfig('app_dirname'); ?>/img/1024px-Infobox_info_icon.svg-white.png">
<span class="contents">
    <?php echo $notice; ?>
</span>
</div>
</div>

