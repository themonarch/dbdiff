<?php namespace toolbox;
if(user::isUserLoggedIn() || user::isGuestLoggedIn()){
	$user_id = user::getUserLoggedIn()->getID();
}else{
	$user_id = null;
}

$_POST['widget_unique_id'] = $widget_unique_id;
$dt = datatableV2::create()
	->set('widget_id', $widget_id)
	->setOrderBy('`last_viewed` desc')
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

$dt->defineCol('sourcename', '[Development] Filter by Table', function($val, $rows, $dt, $col){
    echo utils::htmlEncode($val).'<br><span class="notifications">'.utils::htmlEncode($rows->source_db).'</span>';
});
$dt->setColSetting('sourcename', 'internal_field_name', '`source_db`');

$dt->enableSearch(2, false);
$dt->enableSort(2, false);

$dt->defineCol('targetname', '[Production] Filter by Table', function($val, $rows, $dt, $col){
    echo utils::htmlEncode($val).'<br><span class="notifications">'.utils::htmlEncode($rows->target_db).'</span>';
});
$dt->setColSetting('targetname', 'internal_field_name', '`target_db`');

$dt->setColSetting(0, 'style-td', 'text-align: center;');

$dt->enableSearch(3, false);
$dt->enableSort(3, false);

$dt->defineCol('last_viewed', 'Last Viewed', function($val, $rows, $dt, $col){
	?>
<span title="<?php
            echo $val;
        ?> +0000" class="timeago"><?php
            echo $val;
        ?></span>
<?php });

$dt->defineCol('id', 'Action', function($val, $rows, $dt, $col){ ?>
	<form data-confirm="Are you sure you want to delete this comparison?
	 `<?php
	echo $rows->source_db;
	?>` vs. `<?php
	echo $rows->target_db;
	?>`" id="row_<?php echo $val; ?>"
	data-show_loader="#row_<?php echo $val; ?>"
	data-ajax_replace="true" data-ajax_form="#row_<?php echo $val;
	?>" action="/ajax/delete_comparison/<?php echo $val; ?>" style="display: inline-block;">
	<button style="padding: 3px 5px;" type="submit" class="btn btn-small btn-silver">
		<i class="icon-trash-empty single"></i>
	</button>
    &nbsp;<a href="/compare/<?php
		echo $val; ?>" class="btn btn-small btn-blue">Compare</a>
	</form>
<?php })
	->setColSetting(3, 'style', 'width: 140px;');
$dt->enableSearch(4, false)
    ->set('post_data', urlencode(json_encode($_POST)))
	->setColSetting(2, 'style', 'width: 160px;')
	->setSortInline();

$dt->setSelect('`id`, concat(`source`.`user`, "@",`source`.`host`) as `sourcename`,
concat(`target`.`user`, "@",`target`.`host`) as `targetname`,
`last_viewed`,
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

