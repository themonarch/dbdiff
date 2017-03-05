<?php namespace toolbox;

$tables = array();

$sync = sync::get($profile_id);

//connect to database
$source_conn = $sync->getSourceConnection();
//$source_conn->connect();

$dt = datatableV2::create();

$sql_where = '';
$excluded_tables = $sync->getExcludedTables(true);
if(isset($only_excluded_table) && $only_excluded_table === true){

	if(empty($excluded_tables)){
		$sql_where = ' and `TABLE_NAME` = ""';
	}else{
		$sql_where = ' and `TABLE_NAME` in ('.implode(', ', $excluded_tables).')';
	}
	$dt->setNoResultView(
		page::create()
			->set('notice', 'You haven\'t hidden any tables.')
			->set('container_style', 'style-1')
			->addView('elements/notice.php')
	);

}else{
	if(!empty($excluded_tables)){
		$sql_where = ' and `TABLE_NAME` not in ('.implode(', ', $excluded_tables).')';
	}

	$dt->addView(function($tpl){ ?>
	    <div class="datatable-info datatable-section">
	        <b><?php echo $tpl->synced; ?></b> out of <b><?php
	        echo $tpl->getTotalRows(); ?></b> tables match.
	    </div>
	    <div class="datatable-info datatable-section">
	        <a href="/compare/<?php echo $tpl->profile_id; ?>/edit"
	        data-max_width="1000"
	        data-overlay-id="edit"
	        class="btn btn-small btn-silver">Edit this Comparison</a>
	    </div>
	<?php }, 'footer');
}

//select all tables
$query = $source_conn->query('select `TABLE_NAME`,
	`DATA_LENGTH` + `INDEX_LENGTH` as `size`,
     `TABLE_ROWS`
     from `information_schema`.`TABLES`
     where `table_schema` = '.db::quote($sync->getSourceDB()).$sql_where);

while($row = $query->fetchRow()){
	$tables[$row->TABLE_NAME] = (object)array(
		'table_name' => $row->TABLE_NAME,
		'source_create' => $sync->getSourceCreate($row->TABLE_NAME),
		'target_table_name' => null,
		'target_create' => null,
		'synced' => 'right'
	);
}

//connect
$target_conn = $sync->getTargetConnection();

//select all tables
$query = $target_conn->query('select `TABLE_NAME`,
	`DATA_LENGTH` + `INDEX_LENGTH` as `size`,
     `TABLE_ROWS`
     from `information_schema`.`TABLES`
     where `table_schema` = '.db::quote($sync->getTargetDB()).$sql_where);
while($row = $query->fetchRow()){
	$target_create = $sync->getTargetCreate($row->TABLE_NAME);

	if(isset($tables[$row->TABLE_NAME])){
		$status = 'synced';
		$auto_inc = utils::getStringBetweenTwoStrings($tables[$row->TABLE_NAME]->source_create, 'AUTO_INCREMENT=', ' ');
		$auto_inc2 = utils::getStringBetweenTwoStrings($target_create, 'AUTO_INCREMENT=', ' ');
		$source_create_final = str_replace('AUTO_INCREMENT='.$auto_inc.' ', '', $tables[$row->TABLE_NAME]->source_create);
		$target_create_final = str_replace('AUTO_INCREMENT='.$auto_inc2.' ', '', $target_create);
		if($source_create_final
			!== $target_create_final){
			$status = 'different';
		}


		$tables[$row->TABLE_NAME] = (object)array_merge((array)$tables[$row->TABLE_NAME], array(
			'target_table_name' => $row->TABLE_NAME,
			'target_create' => $target_create,
			'synced' => $status
		));
	}else{
	$tables[$row->TABLE_NAME] = (object)array(
		'table_name' => $row->TABLE_NAME,
		'source_create' => null,
		'target_table_name' => null,
		'target_create' => $target_create,
		'synced' => 'left'
	);
	}
}

    ob_implicit_flush(true);
	$dt->enableSearch(false)
	->enableSort(false)
	->set('left', 0)
	->set('right', 0)
	->set('different', 0)
	->set('synced', 0)
	->setLimit(false)
	->set('profile_id', $profile_id)
    ->setCallback('postRow', function($dt, $row){ ?>
	    <tr class="table_dropdown row_expand-<?php echo $this->data['last_row_id']; ?>">
	        <td style="display: none;"  colspan="<?php echo $dt->getColCount();
	            ?>" class="destination"></td>
	    </tr>
	<?php })
	->setStmt(new data_stmt($tables))
    /*->defineCol('table_name', 'Ignore', function(){
		?>
		<a data-ajax="true"
		data-ajax_replace="true"
		data-output_destination=".row-HvnA"
		data-show_loader=".row-HvnA .showloader"
		href="" class="btn btn-small btn-gray">
                    <input type="checkbox"> Ignore</a>
		<?php
    })*/
    ->defineCol('table_name', $source_conn->getName().'<br>`'.$sync->getSourceDB().'`',
    	function($val, $row){
    		if($row->synced == 'left'){ ?>
			<div style="text-align: center;">
    		<span class="notifications">MISSING</span>
    		</div>
			<?php } else{
				return $row->table_name;
			}
    })
    ->defineCol('synced', 'Vs.', function($val, $row, $dt){
		$dt->{$val}++;
		if($val === 'synced'){ ?>
			<i class="icon-ok"></i><br>Identical
		<?php }elseif($val === 'right'){ ?>
			<i class="icon-right"></i><br>Missing
		<?php }elseif($val === 'left'){ ?>
			<i class="icon-left"></i><br>Missing
		<?php }elseif($val === 'different'){ ?>
			<i class="icon-exchange"></i><br>Different
		<?php }
    })
	->setColSetting(1, 'style', 'width: 80px;')
	->setColSetting(3, 'style', 'width: 190px;')
    ->setCallback('pretd', function($dt, $col, $val, $row, $field_name){
        if($row->synced === 'different'){
            $dt->setColSetting($field_name, 'class', 'diff-different');
        }elseif($row->synced === 'left'){
            $dt->setColSetting($field_name, 'class', 'diff-left');
        }elseif($row->synced === 'right'){
            $dt->setColSetting($field_name, 'class', 'diff-right');
        }elseif($row->synced === 'synced'){
            $dt->setColSetting($field_name, 'class', 'diff-same');
        }
    })
    ->defineCol('target_table_name',
    	$target_conn->getName().'<br>`'.$sync->getTargetDB().'`',
    	function($val, $row){
    		if($row->synced == 'right'){ ?>
			<div style="text-align: center;">
    		<span class="notifications">MISSING</span>
    		</div>
			<?php } else{
				return $row->table_name;
			}
    });


	if(isset($only_excluded_table) && $only_excluded_table === true){

		$dt->defineCol('table_name', 'Details', function($val, $rows, $dt){ ?>
		<div id="row_<?php echo utils::htmlEncode($val); ?>">
		<?php

        $this->data['last_row_id'] = utils::getRandomString(8);
		if($rows->synced == 'synced'){
			$btn_style = 'gray';
		}else{
			$btn_style = 'rose';
		}

        ?><form style="display: inline-block;" method="post" action="/compare/<?php
        echo $dt->profile_id; ?>/table/<?php echo urlencode($val); ?>" <?php
        	?>data-ajax_form="#<?php
        		echo $this->widget_id
        	?> .row_expand-<?php
			echo $this->data['last_row_id']
        	?> .destination " <?php
        	?>data-form_toggle="true" <?php
        	?>data-ajax_replace="false" <?php
        	?>data-show_loader="#<?php echo $this->widget_id;
        		?> .row_expand-<?php echo $this->data['last_row_id']; ?> .destination "><?php
        	?><button type="submit" value="Schema Diff" class="btn btn-<?php
        		echo $btn_style; ?> btn-small"><i class="icon-down-open"></i> &nbsp;Schema Diff</button><?php
        	?></form> <form data-show_loader="#row_<?php echo utils::htmlEncode($val); ?>"
	data-ajax_replace="true" data-ajax_form="#row_<?php echo utils::htmlEncode($val);
	?>" action="/compare/<?php
        echo $dt->profile_id; ?>/table/<?php echo urlencode($val); ?>/unhide" style="display: inline-block;">
	<button title="Move table back to unhidden." style="padding: 5px 5px;" type="submit" class="btn btn-small btn-silver">
		<i class="icon-eye single"></i>
	</button>
	</form>
	</div>
            <?php
    });







	}else{

	    $dt->defineCol('table_name', 'Details', function($val, $rows, $dt){ ?>
	    	<div id="row_<?php echo utils::htmlEncode($val); ?>">
	    	<?php

	        $this->data['last_row_id'] = utils::getRandomString(8);
			if($rows->synced == 'synced'){
				$btn_style = 'gray';
			}else{
				$btn_style = 'rose';
			}

	        ?><form style="display: inline-block;" method="post" action="/compare/<?php
	        echo $dt->profile_id; ?>/table/<?php echo urlencode($val); ?>" <?php
	        	?>data-ajax_form="#<?php
	        		echo $this->widget_id
	        	?> .row_expand-<?php
				echo $this->data['last_row_id']
	        	?> .destination " <?php
	        	?>data-form_toggle="true" <?php
	        	?>data-ajax_replace="false" <?php
	        	?>data-show_loader="#<?php echo $this->widget_id;
	        		?> .row_expand-<?php echo $this->data['last_row_id']; ?> .destination "><?php
	        	?><button type="submit" value="Schema Diff" class="btn btn-<?php
	        		echo $btn_style; ?> btn-small"><i class="icon-down-open"></i> &nbsp;Schema Diff</button><?php
	        	?></form> <form data-confirm="Hide this table? (`<?php echo utils::htmlEncode($val);
		?>`) It will be moved to the hidden tables section."
		data-show_loader="#row_<?php echo utils::htmlEncode($val); ?>"
		data-ajax_replace="true" data-ajax_form="#row_<?php echo utils::htmlEncode($val);
		?>" action="/compare/<?php
	        echo $dt->profile_id; ?>/table/<?php echo urlencode($val); ?>/hide" style="display: inline-block;">
		<button title="Move to Table to Hidden Widget" style="padding-left: 5px 5px;" type="submit" class="btn btn-small btn-silver">
			<i class="icon-eye-off single"></i>
		</button>
		</form>
		</div>
	            <?php
	    });
	}

    $dt->setPaginationDestination('#'.$widget_id)
    ->renderViews();


        ob_implicit_flush(false);