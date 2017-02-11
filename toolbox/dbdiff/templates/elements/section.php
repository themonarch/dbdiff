<div class="section <?php $this->renderViews('section-class'); ?>" data-section-trigger=""><?php
$this->renderViews('section-pre-contents');
	?><div class="contents restrict-width centered">
        <div class="contents-inner">
            <div class="section-content">
<?php $this->renderViews(); ?>
            </div>
        </div>
        <div class="catchall"></div>
    </div>
</div>