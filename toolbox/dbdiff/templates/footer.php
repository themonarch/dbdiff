<?php namespace toolbox; ?>
&copy; <?php echo date('Y', time()); ?> <?php
echo config::get()->getConfig('app_name'); ?>.
<br>
<?php
    if(config::get()->getConfig('environment') !== 'production'
        || (
        accessControl::get()->hasRequirement('webmaster')
        )
    ){ ?>
    <a href="#" data-overlay-id="sql_time">
        Execution Time: <?php echo benchmark::create()->getSessionTime(); ?> Sec.
    </a>
    <?php }else{ ?>
        Execution Time: <?php echo benchmark::create()->getSessionTime(); ?> Sec.
<?php } ?>

 | RAM: <?php echo benchmark::create()->getRamUsage(); ?> MB
 | CPU: <?php echo benchmark::create()->getCpuLoad(); ?>
 | Time: <?php echo date('M d, Y  g:i:s A T'); ?>
<?php if(isset($version)){ ?>
 | <a href="/version">Version <?php echo $version->version; ?></a>
updated <span class="timeago" title="<?php echo $version->date; ?>Z"><?php echo $version->date; ?>Z</span>
<?php } ?>

