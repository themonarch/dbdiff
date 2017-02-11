<?php namespace toolbox;
if($this->limit === false){
	return;
} ?>
<div class="datatable-info datatable-section pagination <?php echo $pagination_class; ?>">
    <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php
        echo $pagination_destination; ?>" data-show_loader="<?php echo $pagination_loader; ?>"
        data-post=<?php echo db::quote($post_data); ?>
        href="<?php echo $this->getPageLink($this->getFirstStart()); ?>"
        class="pagination-button pagination-first btn btn-small btn-link">
        <i class="icon-angle-double-left"></i>
    </a>

    <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php echo
        $pagination_destination; ?>" data-show_loader="<?php echo $pagination_loader; ?>"
        data-post=<?php echo db::quote($post_data); ?>
         href="<?php echo $this->getPageLink($this->getPrevStart()); ?>"
         class="pagination-button pagination-prev btn btn-small btn-link">
        <i class="icon-angle-left"></i>
    </a>

    <?php while($this->nextPage()){  ?>
        <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php
            echo $pagination_destination; ?>" data-show_loader="<?php echo $pagination_loader; ?>"
             data-post=<?php echo db::quote($post_data); ?>
             href="<?php echo $this->getPageLink($this->getPageStart()); ?>" class="pagination-button <?php
                if($this->getPageNum() == $this->getCurrentPage()){
                    ?>  active<?php } ?> btn btn-small btn-link"><?php
                echo $this->getPageNum(); ?></a>
    <?php } ?>

    <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php
        echo $pagination_destination; ?>" data-show_loader="<?php echo $pagination_loader; ?>"
        data-post=<?php echo db::quote($post_data); ?>
         href="<?php echo $this->getPageLink($this->getNextStart());
            ?>" class="pagination-button pagination-next btn btn-small btn-link"><i class="icon-angle-right"></i></a>

    <a data-ajax="true" data-ajax_replace="true" data-output_destination="<?php echo
        $pagination_destination; ?>" data-show_loader="<?php echo $pagination_loader; ?>"
        data-post=<?php echo db::quote($post_data); ?>
        href="<?php echo $this->getPageLink($this->getLastStart()); ?>" class="pagination-button pagination-last
                btn btn-small btn-link">
        <i class="icon-angle-double-right"></i>
    </a>
</div>