<?php namespace toolbox;
$sql_where = '`sync_id` = '.db::quote(router::get()->getParam('profile_id'));
if(isset($table)){
	$sql_where .= ' and `table` = '.db::quote($table);
}
datatableV2::create()
    ->setSelect('`status`, `date_updated`, `status_msg`, `sync_id`, `id`, LEFT(`sql`, 81) as `sql`')
    ->setFrom('`sql_history`')
    ->setWhere($sql_where)
    ->defineCol('sql', 'SQL', function($val, $cols){ ?>
    	<a href="/compare/<?php
        	echo $cols->sync_id; ?>/sql_history/<?php echo $cols->id; ?>"
            data-overlay-id="view_sql"
            title="Click to view entire SQL" style="white-space: pre-wrap;"><?php
    		echo substr($val, 0, 80);
			if(strlen($val) >= 81){
    			echo ' ...';
    		};
    	?></a>
    <?php })
    ->defineCol('status', 'Status', function($val, $cols){ ?>
    	<?php echo $val;
    	if($cols->status_msg == ''){
    		return;
    	}
    	?><span class="tooltip" title="<?php echo utils::htmlEncode($cols->status_msg); ?>"><i class="icon-info-circled"></i></span>
    <?php })
    ->setColSetting(1, 'style', 'width: 140px;')
	->setSort(2, 'desc')
	->enableSort(3, false)
	->enableSearch(3, false)
	->enableSearch(2, false)
	->setSortInline()
    ->defineCol('date_updated', 'Last Check-in', function($val){ ?>
    	<span class="timeago" title="<?php echo $val; ?>+0000"><?php echo $val; ?></span>
    <?php })
    ->setColSetting(2, 'style', 'width: 160px;')
    ->setColSetting(3, 'style', 'width: 160px;')
    ->defineCol('id', 'Actions', function($val, $cols){ ?>
    	<?php if(!in_array($cols->status, array('completed', 'failed'))){ ?>
        <a href="#"
            class="btn btn-small btn-red"
            data-overlay-id="connect"
            title="Edit Connection">Kill</a>
        <?php } ?>
        <a href="/compare/<?php
        	echo $cols->sync_id; ?>/sql_history/<?php echo $cols->id; ?>"
            class="btn btn-small btn-silver"
            data-overlay-id="view_sql"
            title="Edit Connection">View SQL</a>
    <?php })
    ->setPaginationDestination('#'.$widget_id)
    ->setPaginationLink($widget_url)
    ->renderViews();
