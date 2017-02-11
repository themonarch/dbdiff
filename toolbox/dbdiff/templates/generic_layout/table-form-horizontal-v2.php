<?php namespace toolbox;
if(!isset($sidelabels)){
	$sidelabels = true;
}
$colspan = 1;
if($sidelabels){
	$colspan = 2;
}
$this->colspan = $colspan;
if(!isset($show_steps)){
	$show_steps = true;
}
?>
<table class="steps">
	<tbody>
		<?php
		$count = 1;
		$real_count = 1;
		foreach ($rows as $name => $view) {
		    $centered = isset($view->centered) ? $view->centered : false;
		    if($count !== 1){ ?><tr>
                    <?php if($show_steps){ ?><td class="path hideMobile"><div class="path-line"></div></td><?php } ?>
                    <td style="padding: 6px;" colspan="<?php echo $colspan; ?>"><div class="catchall"></div></td>
                </tr><?php } ?>
                <tr>
                    <?php if($show_steps){ ?><td class="path hideMobile">
                     <?php if(!isset($view->row) || $view->row !== false){ ?>
                     <div class="path-content <?php if($centered){ ?>centered<?php } ?>">
                        <?php echo $count++; ?>
                    </div><?php } ?>
                    <div class="path-line <?php if($centered && $count === 2){ ?>bottom half<?php } ?>"></div></td><?php } ?>
                    <?php if($sidelabels){ ?><td class="first"><div class="inner"><?php echo $name; ?></div></td><?php } ?>
                    <td><?php
                            $view->renderViews();
                    ?></td>
                </tr>
                <tr>
                     <?php if($show_steps){ ?><td class="path hideMobile"><div class="path-line"></div></td><?php } ?>
                    <td>
                		<?php $this->renderViews('post-row-'.$real_count++); ?>
                	</td>
                </tr>
		<?php } ?>
                <tr>
                    <?php if($show_steps){ ?><td class="path hideMobile"><div class="path-line"></div></td><?php } ?>
                    <td style="padding: 6px;">
                    	<div class="catchall"></div>
                        <?php $this->renderViews('pre-submit'); ?>
                    	<div class="catchall spacer-1"></div>
                	</td>
                </tr>
                <tr>
                    <?php if($show_steps){ ?><td class="path hideMobile">
                    	<div class="path-line half"></div>
                    <div class="path-content centered">
                        <i class="icon-ok"></i>
                    </div></td><?php } ?>
                    <td colspan="<?php echo $colspan; ?>" style="text-align: center;">
                        <?php $this->renderViews('submit'); ?>
                    </td>
                </tr>
	</tbody>
</table>