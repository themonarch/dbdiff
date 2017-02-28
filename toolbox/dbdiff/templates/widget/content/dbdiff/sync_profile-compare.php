<?php namespace toolbox;

$tables = array();

$sync = sync::get($profile_id);

//connect to database
$source_conn = $sync->getSourceConnection();
//$source_conn->connect();

//select all tables
$query = $source_conn->query('select `TABLE_NAME`,
	`DATA_LENGTH` + `INDEX_LENGTH` as `size`,
     `TABLE_ROWS`
     from `information_schema`.`TABLES`
     where `table_schema` = '.db::quote($sync->getSourceDB()));
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
//$target_conn->connect();

//select all tables
$query = $target_conn->query('select `TABLE_NAME`,
	`DATA_LENGTH` + `INDEX_LENGTH` as `size`,
     `TABLE_ROWS`
     from `information_schema`.`TABLES`
     where `table_schema` = '.db::quote($sync->getTargetDB()));
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

/*usort($tables, function($a, $b){

	if($a->synced === 'synced' && $b->synced !== 'synced'){
		return 999999;
	}
	if($b->synced === 'synced'){
		return -999999;
	}
	//return strcasecmp($a->table_name, $b->table_name);

});*/




$dt = datatableV2::create()
	->enableSearch(false)
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
    ->defineCol('table_name', $source_conn->getName().'<br>`'.$sync->getSourceDB().'`', function($val, $row){
		return $row->table_name;
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
	->setColSetting(3, 'style', 'width: 130px;')
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
    ->defineCol('target_table_name', $target_conn->getName().'<br>`'.$sync->getTargetDB().'`', function($val, $row){
			return $row->table_name;
    })
    ->defineCol('table_name', 'Details', function($val, $rows, $dt){
        $this->data['last_row_id'] = utils::getRandomString(8);
		if($rows->synced == 'synced'){
			$btn_style = 'gray';
		}else{
			$btn_style = 'rose';
		}

        ?><form style="display: inline-block;" method="post" action="/compare/<?php
        echo $dt->profile_id; ?>/table/<?php echo $val; ?>" <?php
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
        		echo $btn_style; ?> btn-small">Schema Diff</button><?php
        	?></form>
            <?php


    })

    ->setPaginationDestination('#'.$widget_id)
	->addView(function($tpl){ ?>
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
	<?php }, 'footer')
    ->renderViews();

