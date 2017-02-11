<?php namespace toolbox; ?>
<div class="nav hideMobile hideTablet" id="main_menu_nav">
    <?php if(!empty($links)) foreach($links as $id => $link){
        $link->renderViews();
    } ?>
</div>