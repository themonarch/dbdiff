<?php
namespace toolbox;
if(!isset($datalist)){
    return;
}
reset($datalist);
$has_data = true;
$limit = ceil(count(get_object_vars($datalist))/2);
$current = 1;
$header_rendered = false;
if(!isset($datalist_header)){
    $datalist_header = '&nbsp;';
}
?>
<div class="grid-12">
    <div class="datatable style2">




<?php
while($has_data){ ?>


<?php
if($current === 1 && $header_rendered === false){
$header_rendered = true;
?>
<div class="grid-12">
    <table class="table style3 datalist">
        <tbody>
        <tr class="even"><td colspan="2" class="datatable-row-header"><?php echo $datalist_header; ?></td></tr>
        </tbody>
    </table>
</div>
<?php } ?>

<div class="grid-6 grid-s-12">
        <table class="table style3 datalist">
            <tbody>
            <?php
                while($label = key($datalist)){
                    $value = current($datalist); ?>
                    <tr class="item">
                        <td class="label"><?php echo $label; ?></td>
                        <td class="value"><?php
                            //echo utils::emptyPlaceholder($value);
                            //utils::vd($value);
                            form::textField()
                                ->setTypeText()
                                ->setName($label)
                                ->setLabel(false)
                                ->setValue(utils::emptyPlaceholder($value))
                                ->setDisabled()
                                ->render();
                        ?></td>
                    </tr><?php
                if(next($datalist) === false){
                    $has_data = false;
                }elseif($current++ >= $limit){
                    $current = 1;
                    break;
                }
}

            ?>
            </tbody>
        </table>
</div>
<?php } ?>
<div class="catchall"></div>
</div>
<div class="catchall"></div>
</div>
<div class="catchall"></div>