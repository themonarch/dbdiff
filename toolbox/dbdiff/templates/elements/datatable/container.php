<?php namespace toolbox; ?>
<?php

if(isset($_GET['search_type'])){
    foreach ($_GET['search_type'] as $col => $type) {
        $this->setColSetting($this->getColFieldName($col), 'search_type', $type);
    }
}

if(
	$this->hasCallback('no_data')
    && $this->getTotalRows() === 0
    && $this->getCallback('no_data') === false
){
    return;
}

if(isset($this->widget_url)){
    $this->setPaginationLink($this->widget_url);
}
?>
<?php if(!isset($tr_only) || $tr_only !== true){ ?>
<div id="<?php echo $datatable_id; ?>">
    <?php $this->renderViews('pre-table'); ?>
<div class="datatable <?php echo $container_class; ?>">
<a style="display: none;" data-ajax="true"
	data-ajax_replace="true" data-output_destination="<?php
    echo $pagination_destination; ?>" data-show_loader="<?php
    echo $pagination_loader; ?>"
    data-post=<?php echo db::quote($post_data); ?>
	class="link" href="<?php
	echo $this->getLink();
   ?>"></a>
	<div class="table-container <?php if($resizable) echo 'resizable'; ?>">

	<table class="table <?php echo $table_class; ?>">
		<thead>
			<tr>
		    <?php foreach($this->colDefinitions as $key => $definitions){ ?>
                   <th class="<?php
                   echo $this->getColSetting($key, 'class');

                   if($this->getColSetting($this->getColFieldName($key), 'is_int') === true){
                   	echo ' integer';
                   }
				   if($this->someSearchable($key)){
				   	 echo ' '.$sort_class;
			   		}
                   	?>" style="<?php
                   echo $this->getColSetting($key, 'style'); ?>"><?php
                   //utils::vd($this->getColSetting($this->getColFieldName($key), 'indexes'));
                   if($this->isSortable($key)){ ?><a data-ajax="true"
               			data-ajax_replace="true" data-output_destination="<?php
				        echo $pagination_destination; ?>" data-show_loader="<?php
			            echo $pagination_loader; ?>"
				        data-post=<?php echo db::quote($post_data); ?>
        			class="padding" href="<?php
					echo $this->getSortLink($key);
                   ?>"><?php } else { ?><span class="padding"><?php }
                   //loop indexes
                   $indexes = $this->getColSetting($this->getColFieldName($key), 'indexes');
                   if(!empty($indexes))
                   foreach($indexes as $index_key => $index_type){ ?>
                    <i class="icon-key index_icon <?php echo $index_type; ?>"></i>
                   <?php } ?>
                    <span class="colname"><?php echo $this->getColDisplayName($key); ?></span>
                    <?php if($this->isSortable($key)){
                    ?><i class="<?php if($this->getSort($key) === 'asc'){
                    	echo 'icon-sort-up';
                    }elseif($this->getSort($key) === 'desc'){
						echo 'icon-sort-down';
                    }elseif($this->getSort($key) === 'none'){
                    	echo 'icon-sort';
                    } ?> datatable-sort"></i><?php
                    }
                   if($this->isSortable($key)){ ?></a><?php }
				   else{ ?></span><?php } ?>
				   <?php if($this->isSearchable($key)){ ?>
				   <?php if($this->getColSetting($this->getColFieldName($key), 'search_type') === 'exact'){ ?>
				   		<span class="icon_input exact_match-toggle">
			   				<a href="#" title="Exact Match: ON (Click to turn off)" class="btn btn-tiny btn-gray">
			   				    <button name="search_type[<?php echo $key;
			   					?>]" value="exact" style="display: none;"></button><span>exact</span></a>
	   					</span>
				   	<?php }else{ ?>
				   		<span class="icon_input exact_match-toggle">
				   			<a href="#" title="Exact Match: OFF (Click to turn on)" class="btn btn-tiny btn-silver">
				   			    <button name="search_type[<?php echo $key;
				   				?>]" value="filter" style="display: none;"></button><span>filter</span></a></span>
			   		<?php } ?>
                   	<input type="text"
                   		name="search[<?php echo $key; ?>]" value="<?php
                   			echo utils::htmlEncode($this->getSearch($key)); ?>" placeholder="<?php
                   			echo $this->getColDisplayName($key);
                   			?>"><?php if($this->getSearch($key) !== ''){ ?>
               				<i class="icon-cancel-circled icon_input_right clear_search"></i>
                   			<?php } ?>
               	<?php }elseif($this->someSearchable()){ ?>
                   	<input disabled="disabled" type="text" placeholder="<?php
                   			echo $this->getColDisplayName($key);
                   			?>"
                   		name="search[<?php echo $key; ?>]" value="<?php
                   			echo utils::htmlEncode($this->getSearch($key)); ?>">
           		<?php } ?>
                   </th>
            <?php } ?>
			</tr>
		</thead>
		<tbody>
<?php }
$even_odd = false;
$this->row_count = 0;
while($row = $this->getNextRow()){ ?>

    <?php
            //we need to start with the header first if one was
            //defined or changed since last display
            if($this->displayHeader){
                //loop fields until we find the header
                foreach($row as $key => $value){
                    if($this->isHeader($key) && $this->currentHeader !== $value){
                    	//call_user_func($this->headerDefinitions[$key]['callback'], $even_odd, $value, $key, $row, $this);
						$this->headerDefinitions[$key]['callback']($even_odd, $value, $key, $row, $this);
                    	$this->currentHeader = $value;
                        break;
                    }
                }
            }
	$this->row_id = utils::getRandomString();
    if(!isset($render_tr) || $render_tr === true){
    ?>
    <tr class="<?php echo ($even_odd = !$even_odd) ? 'even' : 'odd'; ?> <?php
    if(isset($tr_class)){
        echo $tr_class;
    } echo ' row-'.$this->row_id; ?>">
    <?php foreach($this->colDefinitions as $key => $definitions){
			$this->getCallback(
				'pretd',
				$key,
				isset($row->{$definitions['field_name']}) ? $row->{$definitions['field_name']} : null,
				$row,
				$definitions['field_name']
			);
            ?>
           <td style="<?php
               echo $this->getColSetting($key, 'style-td');
           ?>" data-col="<?php echo $this->getColFieldName($key); ?>" class="<?php
           echo $this->getColSetting($this->getColFieldName($key), 'class');

           if($this->getColSetting($this->getColFieldName($key), 'is_int') === true){
           	echo ' integer';
           }
           if($this->getColSetting($this->getColFieldName($key), 'is_nullable') === true){
           	echo ' nullable';
           }
           if($this->getColSetting($this->getColFieldName($key), 'is_updated') === true){
           	echo ' updated';
           }
           ?>"><?php
            echo $this->getColValue(
                            $key,
                            isset($row->{$definitions['field_name']}) ? $row->{$definitions['field_name']} : null,
                            $row
                        );
            ?></td>
    <?php } ?>
     </tr>
<?php }

$this->getCallback('postRow', $row); ?>
<?php
    if($this->getTotalRows() === ++$this->row_count){
        $this->getCallback('lastRow', $row, $even_odd);
    }

?>
<?php } ?>
<?php if(!isset($tr_only) || $tr_only !== true){ ?>
<?php $this->renderViews('last_row'); ?>
		</tbody>
	</table>
    <?php
    if($this->getTotalRows() === 0){
        $this->renderViews('no_data');
    }
    ?>

	</div>
    <?php $this->renderViews('pre-pagination'); ?>
	<?php
	if($this->getTotalPages() > 1){
	$this->renderViews('pagination');
	}
	?>
    <?php $this->renderViews('footer'); ?>
	<div class="catchall"></div>
</div>
    <?php $this->renderViews('post-table'); ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
    <?php
    $input_focus = false;
    if(isset($_GET['input_focus'])){
       $input_focus = $_GET['input_focus'];
    }
    if($input_focus !== false){ ?>
        var input = $('#<?php echo $datatable_id;
            ?> table > thead > tr > th > input[name="<?php echo $input_focus; ?>"]');
        input.focus();
        var temp_val = input.val();
        input.val('');
        input.val(temp_val);
    <?php } ?>
    resize_datatable();
});
</script>
<?php } ?>