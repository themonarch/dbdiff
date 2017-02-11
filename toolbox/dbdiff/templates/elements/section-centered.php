<?php namespace toolbox;

    $this
        ->addView(function(){ ?><div class="dummy"></div><?php }, 'section-pre-contents')
        ->render('elements/section.php');
