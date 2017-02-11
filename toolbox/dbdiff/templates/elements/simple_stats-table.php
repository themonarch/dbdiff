<?php namespace toolbox;
if(!isset($stats_array) || !is_array($stats_array)){
    $stats_array = array();
};
$loop_count = 0;
if(!isset($grid)){
    $grid = 3;
}
$rand_str = utils::getRandomString();
?>
<div class="widget-subheader clearfix">
    <?php echo $description; ?>
<?php $this->renderViews('widget-subheader'); ?>
</div>
<table data-tab-content="" class="table style1">
    <thead>
        <tr>
            <?php
            if(!empty($stats_array))
                foreach(array_keys($stats_array[key($stats_array)]) as $key){ ?>
                    <th><?php echo $key; ?></th>
            <?php } ?>
            <?php if(!isset($show_date) || $show_date !== false){ ?>
            <th>date</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach($stats_array as $time => $array){ ?>
        <?php if(isset($limit) && $loop_count++ > $limit) break; ?>
            <tr>
            <?php foreach($array as $key => $value){ ?>
                <td><?php echo $value; ?></td>
            <?php } ?>
            <?php if(!isset($show_date) || $show_date !== false){ ?>
                <td><span class="timeago" title="<?php echo $time; ?> +0000"><?php echo $time; ?></span></td>
            <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php $this->renderViews('widget-content'); ?>
