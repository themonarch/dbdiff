<?php
namespace toolbox;
if(!isset($bignums) || count($bignums) === 0){
    $bignums = array();
    $grids = 0;
}else{
	$grid_count = count($bignums);
    $grid_size = 12/$grid_count;
	$grids = floor($grid_size);
	if($grid_count > 8){
		$grids = 2;
	}elseif($grid_size != $grids){
		$grids = $grids.'-'.$grid_count;
	}

}
if(isset($grid_min) && $grids < $grid_min){
    $grids = $grid_min;
}

$grid_classes = 'grid-'.$grids;

if(isset($grid_min_m) && $grids < $grid_min_m){
    $grid_classes .= ' grid-m-'.$grid_min_m;
}

if(isset($grid_min_s) && $grids < $grid_min_s){
    $grid_classes .= ' grid-s-'.$grid_min_s;
}

?>
<?php $this->renderViews('before_bignum_container'); ?>
<div class="bignum-container">
    <?php if(empty($bignums)){ ?>
        <div class="catchall spacer-1"></div>
        <?php
        	page::create()
        		->set('notice', 'No Data to Display.')
        		->addView('elements/notice.php')->renderViews();
    } ?>
<?php foreach ($bignums as $key => $bignum) { ?>
<div class="<?php echo $grid_classes; ?> bignum">
	<?php $this->renderViews('content'); ?>
    <div class="bignum-num"><?php
        if(isset($bignum['anchor-tag-contents'])){
            echo '<a '.$bignum['anchor-tag-contents'].'>';
        }
        if(isset($bignum['num'])){
            if(isset($bignum['pre-units'])){
                ?><span class="pre-units"><?php echo $bignum['pre-units']; ?></span><?php
            }
            echo formatter::number_commas($bignum['num']);
        }else{
            echo '--';
        }

        if(isset($bignum['units'])){
            ?><span class="units"><?php echo $bignum['units']; ?></span>
        <?php }
        if(isset($bignum['anchor-tag-contents'])){
            echo '</a>';
        }
        ?></div>
    <div class="bignum-subtitle"><?php echo $bignum['subtitle']; ?></div>
    <?php if(isset($bignum['chart'])){
        ?><div class="bignum-chart"><?php $bignum['chart']->renderViews(); ?></div>
    <?php } ?>
</div>
<?php } ?>
<div class="catchall"></div>
</div>
