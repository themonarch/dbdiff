<?php namespace toolbox; ?>
<div class="form_panel style3">

<div class="grid-6">
<div style="text-align: center; margin: -7px 0px 0px; position: relative; z-index: 1; font-variant: all-small-caps;">[ Development ]</div>
	<div class="header-line style4">
	    <div class="inner"><?php echo $source_name; ?></div>
	    <div class="gradient-line"></div>
	</div>
	<div style="text-align: center; margin: -7px 0px 0px; position: relative; z-index: 1;">`<?php echo $source_db; ?>`</div>
</div>

<div class="grid-6">
<div style="text-align: center; margin: -7px 0px 0px; position: relative; z-index: 1; font-variant: all-small-caps;">[ Production ]</div>
	<div class="header-line style4">
	    <div class="inner"><?php echo $target_name; ?></div>
	    <div class="gradient-line"></div>
	</div>
	<div style="text-align: center; margin: -7px 0px 0px; position: relative; z-index: 1;">`<?php echo $target_db; ?>`</div>
</div>


<div class="catchall"></div>
<?php
datatableV2::create()
	->enableSearch(false)
	->enableSort(false)
	->setLimit(false)
	->set('widget_id', $widget_id)
	->set('source_create', $source_create.';')
	->set('target_create', $target_create.';')
	->setContainerClass('diff-table')
	->setTableClass(' shrink-width')
	->setStmt(new data_stmt($diff))
    ->defineCol('line', '', function($val){
    	$val = str_replace('<<<delnl>>>', "<br>", $val);
		if(utils::stringContains($val, array('</del>', '</ins>'))){ ?>
			<div class="diff-contents"><div class="diff-linediff"><?php echo $val; ?></div></div>
		<?php }else{ ?>
			<div class="diff-contents"><?php echo $val; ?></div>
		<?php }
	})
	->setColSetting('line', 'class', 'diff diff-source')
    ->defineCol('line2', '', function($val, $cols){
    	$cols->line = str_replace('<<<insnl>>>', "<br>", $cols->line);
		if(utils::stringContains($cols->line, array('</del>', '</ins>'))){ ?>
			<div class="diff-contents"><div class="diff-linediff"><?php echo $cols->line; ?></div></div>
		<?php }else{ ?>
			<div class="diff-contents"><?php echo $cols->line; ?></div>
		<?php }
	})
    ->setCallback('lastRow', function($dt, $row, $even_odd){ ?>

		<tr class="">
			<td class="diff diff-source" data-col="line">
				<div class="catchall spacer-3"></div>
				<div class="header-line style2">
    <div class="inner">Alter to Look Like Production</div>
    <div class="gradient-line"></div>
</div>
			<div class="catchall spacer-1"></div>
<table style="width: 100%;">
	<tbody>
<?php
$updater = new dbStructUpdater();
$lines = 0;
$changes = false;
foreach($updater->getUpdates($dt->source_create, $dt->target_create) as $sql){
$editor1_id = utils::getRandomString();
?>
		<tr>
			<td style="width: 20px; vertical-align: middle; padding: 0px 5px;"><input type="checkbox" name="alter" class="btn btn-tiny btn-blue" value="run"></td>
			<td><div class="datatable" id="editor_<?php
	echo $editor1_id; ?>" style="width: 100%;"><?php
	echo $sql.';'."\n";
	$lines += substr_count( $sql, "\n");
	$changes = true;
	?></div><script>
    var editor_<?php echo $editor1_id; ?> = ace.edit("editor_<?php echo $editor1_id; ?>");
    editor_<?php echo $editor1_id; ?>.setTheme("ace/theme/twilight");
    editor_<?php echo $editor1_id; ?>.setOption("maxLines", Infinity);
    editor_<?php echo $editor1_id; ?>.setOption("readOnly", true);
    editor_<?php echo $editor1_id; ?>.setOption("wrap", true);
    editor_<?php echo $editor1_id; ?>.setOption("showGutter", false);
    editor_<?php echo $editor1_id; ?>.setOption("autoScrollEditorIntoView", false);
    editor_<?php echo $editor1_id; ?>.setOption("showPrintMargin", false);
    editor_<?php echo $editor1_id; ?>.getSession().setMode("ace/mode/sql");
    editor_<?php echo $editor1_id; ?>.getSession().setFoldStyle("manual");

</script></td>
		</tr><tr><td><div class="catchall spacer-1"></div></td><td></td></tr><?php
} ?>
	</tbody>
</table><?php
if(!$changes) echo '#table schemas match!';
?></td>
			<td class="diff diff-target" data-col="line2">


				<div class="catchall spacer-3"></div>
<div class="header-line style2">
    <div class="inner">Alter to Look Like Development</div>
    <div class="gradient-line"></div>
</div>
			<div class="catchall spacer-1"></div>
		<table style="width: 100%;">
	<tbody>
<?php
$updater = new dbStructUpdater();
$lines = 0;
$changes = false;
foreach($updater->getUpdates($dt->target_create, $dt->source_create) as $sql){
$editor2_id = utils::getRandomString();
?>
		<tr><td style="width: 20px; vertical-align: middle; padding: 0px 5px;">
	<input type="checkbox" name="alter" class="btn btn-tiny btn-blue" value="run"></td>
			<td><div class="datatable" id="editor_<?php
	echo $editor2_id; ?>" style="width: 100%;"><?php
	echo $sql.';'."\n";
	$lines += substr_count( $sql, "\n");
	$changes = true;
	?></div><script>
    var editor_<?php echo $editor2_id; ?> = ace.edit("editor_<?php echo $editor2_id; ?>");
    editor_<?php echo $editor2_id; ?>.setTheme("ace/theme/twilight");
    editor_<?php echo $editor2_id; ?>.setOption("maxLines", Infinity);
    editor_<?php echo $editor2_id; ?>.setOption("readOnly", true);
    editor_<?php echo $editor2_id; ?>.setOption("wrap", true);
    editor_<?php echo $editor2_id; ?>.setOption("showGutter", false);
    editor_<?php echo $editor2_id; ?>.setOption("autoScrollEditorIntoView", false);
    editor_<?php echo $editor2_id; ?>.setOption("showPrintMargin", false);
    editor_<?php echo $editor2_id; ?>.getSession().setMode("ace/mode/sql");
    editor_<?php echo $editor2_id; ?>.getSession().setFoldStyle("manual");

</script></td></tr><tr><td><div class="catchall spacer-1"></div></td><td></td></tr><?php
} ?>
	</tbody>
</table><?php
if(!$changes) echo '#table schemas match!';
?></td>
		</tr>
		<tr class="">
			<td class="diff diff-source" data-col="line">
				<div class="catchall spacer-1"></div>
			<div style="text-align: center;">
<form data-show_loader="#<?php echo $dt->widget_id; ?>"
data-ajax_replace="true" data-form_toggle="false"
data-ajax_form="#<?php echo $dt->widget_id; ?>"
action="/dbdiff/manage_databases/sync_profile/ZrHY/compare/table/db_connections"
method="post" style="display: inline-block;">
	<button class="btn btn-black btn-medium" value="View Diff" type="submit">Run Selected SQL on Development</button>
</form>

			</div>
<div class="catchall spacer-2"></div>
			</td>
			<td class="diff diff-target" data-col="line2">
				<div class="catchall spacer-1"></div>
			<div style="text-align: center;">
<form data-show_loader="#<?php echo $dt->widget_id; ?>"
data-ajax_replace="true" data-form_toggle="false"
data-ajax_form="#<?php echo $dt->widget_id; ?>"
action="/dbdiff/manage_databases/sync_profile/ZrHY/compare/table/db_connections"
method="post" style="display: inline-block;">
	<button class="btn btn-black btn-medium" value="View Diff" type="submit">Run Selected SQL on Production</button>
</form>

			</div>

			</td>
		</tr>
        <?php
    })
	->setColSetting('line2', 'class', 'diff diff-target')
    ->renderViews();

	?>

<div class="catchall spacer-2"></div>

</div>
<div class="catchall-border style2"></div>