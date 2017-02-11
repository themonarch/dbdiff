<?php namespace toolbox;

if(!isset($sidelabels)){
	$sidelabels = true;
}
$colspan = 1;
if($sidelabels){
	$colspan = 2;
}
$this->colspan = $colspan;

if(!isset($centered_steps)){
	$centered_steps = array();
}
?>
<table class="steps">
	<tbody>
		<?php
		$count = 1;
		foreach ($rows as $name => $view) {
		    $centered = true;
		    if(isset($centered_steps[$count-1]) && $centered_steps[$count-1] !== 'yes'){
                $centered = false;
		    }
		    ?>
                <?php if($count !== 1){ ?><tr>
                    <td class="path hideMobile"><div class="path-line"></div></td>
                    <td style="padding: 6px;" colspan="<?php echo $colspan; ?>"><div class="catchall"></div></td>
                </tr><?php } ?>
                <tr>
                    <td class="path hideMobile">
                    <div class="path-content <?php if($centered){ ?>centered<?php } ?>">
                        <?php echo $count++; ?>
                    </div><div class="path-line <?php if($centered && $count === 2){ ?>bottom half<?php } ?>"></div></td>
                    <?php if($sidelabels){ ?><td class="first"><div class="inner"><?php echo $name; ?></div></td><?php } ?>
                    <td><?php
                        if(get_class($view) == 'toolbox\page'){
                            $view->renderViews();
                        }else{
                            $view->render();
                        }
                    ?></td>
                </tr>
                <?php $this->renderViews('post-row-'.($count-1)); ?>
		<?php } ?>
                <tr>
                    <td class="path hideMobile"><div class="path-line"></div></td>
                    <td style="padding: 6px;">
                    	<div class="catchall"></div>
                        <?php $this->renderViews('pre-submit'); ?>
                    	<div class="catchall spacer-1"></div>
                	</td>
                </tr>
                <tr>
                    <td class="path hideMobile">
                    	<div class="path-line half"></div>
                    <div class="path-content centered">
                        <i class="icon-ok"></i>
                    </div></td>
                    <td colspan="<?php echo $colspan; ?>" style="text-align: center;">
                        <?php $this->renderViews('submit'); ?>
                    </td>
                </tr>
	</tbody>
</table>