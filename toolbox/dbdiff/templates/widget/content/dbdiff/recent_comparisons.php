<?php namespace toolbox;
if(user::isUserLoggedIn() || user::isGuestLoggedIn()){
	$user_id = user::getUserLoggedIn()->getID();
}else{
	$user_id = null;
}

$_POST['widget_unique_id'] = $widget_unique_id;
$dt = datatableV2::create()
	->set('widget_id', $widget_id)
	->setOrderBy('`date_created_or_last_synced` desc')
	->enableSearch()
	->clearViews('no_data')
	->addView(function(){ ?>
		<div class="catchall-border style3"></div>
		<div class="catchall spacer-3"></div>
<div class="notice-container ">
<div class="notice">
    <img class="icon" src="//dev.dbdiff.com/assets/app/img/1024px-Infobox_info_icon.svg-white.png">
<span class="contents">
    No recent comparisons found.</span>
</div>
</div>
		<div class="catchall spacer-3"></div>
	<?php }, 'no_data');

$dt->defineCol('sourcename', 'Connection #1', function($val, $rows, $dt, $col){
    echo utils::htmlEncode($val.' ('.($rows->source_db).')');
});
$dt->setColSetting('sourcename', 'internal_field_name', '`source`.`name`');

$dt->enableSearch(2, false);
$dt->enableSort(2, false);

$dt->defineCol('targetname', 'Connection #2', function($val, $rows, $dt, $col){
    echo utils::htmlEncode(($val).' ('.($rows->target_db).')');
});
$dt->setColSetting('targetname', 'internal_field_name', '`target`.`name`');

$dt->enableSearch(3, false);

$dt->defineCol('id', 'Action', function($val, $rows, $dt, $col){ ?>
    <a href="/compare/<?php
		echo $val; ?>" class="btn btn-small btn-blue">Compare</a>
<?php });
$dt->enableSearch(4, false)
    ->set('post_data', urlencode(json_encode($_POST)))
	->setColSetting(2, 'style', 'width: 140px;')
	->setSortInline();

$dt->setSelect('`id`, `source`.`name` as `sourcename`,
`target`.`name` as `targetname`,
`source_db`,
`target_db`,
`sync_direction`,
`tables`,
`tables_excluded`,
`tables_included`')
    ->setFrom('`db_sync_profiles`
        left join `db_connections` `source` on (`source_conn_id` = `source`.`connection_id`)
        left join `db_connections` `target` on (`target_conn_id` = `target`.`connection_id`)
    ')
	->setWhere('db_sync_profiles.`user_id` = '.db::quote($user_id))
    ->enableSort()
    ->setPaginationDestination('#'.$widget_id)->addView(function($tpl){ ?>
    <div class="datatable-info datatable-section">
<span>Found <b><?php echo $tpl->getTotalRows(); ?></b> results.</span>
    </div>
<?php }, 'pre-pagination')
    ->renderViews();

