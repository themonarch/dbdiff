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
	->enableSort(0, false)
	->enableSearch(3, false)
	->enableSearch(2, false)
	->setSortInline()
    ->defineCol('date_updated', 'Last Check-in', function($val){ ?>
    	<span class="timeago" title="<?php echo $val; ?>+0000"><?php echo $val; ?></span>
    <?php })
    ->setColSetting(2, 'style', 'width: 160px;')
    ->setColSetting(3, 'style', 'width: 160px;')
    ->defineCol('id', 'Actions', function($val, $rows){ ?>
    	<?php if(in_array($rows->status, array('running', 'pending'))){ ?>
	<form data-confirm="Are you sure you want to attempt to kill this query?"
	id="row_<?php echo $val; ?>"
	data-show_loader="#row_<?php echo $val; ?>"
	data-ajax_replace="true" data-ajax_form="#row_<?php echo $val;
	?>" action="/compare/<?php
        	echo $rows->sync_id; ?>/kill_query/<?php echo $val; ?>" style="display: inline-block;">
	<button style="padding: 3px 5px;" type="submit" class="btn btn-small btn-red">
		Kill
	</button>
	</form>
        <?php } ?>
        <a href="/compare/<?php
        	echo $rows->sync_id; ?>/sql_history/<?php echo $rows->id; ?>"
            class="btn btn-small btn-silver"
            data-overlay-id="view_sql"
            title="Edit Connection">View SQL</a>
    <?php })
    ->setPaginationDestination('#'.$widget_id)
    ->setPaginationLink($widget_url)
    ->renderViews();
