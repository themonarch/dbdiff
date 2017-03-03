<?php namespace toolbox;
$_POST['widget_unique_id'] = $widget_unique_id;
datatableV2::create()
	->setPaginationLimit(1)
	->setLimit(5)
	->set('container_class', 'style2')
	->set('name', $name)
	->enableSearch(1, false)
	->setSortInline()
    ->setSelect('*,
        "N/A" as `tables`,
        "N/A" as `size`')
    ->setFrom('`information_schema`.`SCHEMATA`')
    ->set('db', $connection_id)
	->setPaginationDestination('#'.$widget_id)
    ->set('post_data', urlencode(json_encode($_POST)))
    ->defineCol('SCHEMA_NAME', 'Database')
    ->defineCol('SCHEMA_NAME', 'Actions', function($val, $rows, $dt){ ?>
            <button type="submit" name="<?php echo $dt->name; ?>"
                    class="btn btn-small btn-blue" value=<?php
                    echo db::quote($val); ?>>Choose Database</button>
    <?php })
    ->renderViews();
