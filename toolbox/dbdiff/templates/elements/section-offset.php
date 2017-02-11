<?php namespace toolbox;

    $this
        ->addView(function(){ ?> centered <?php }, 'section-class')
        ->render('elements/section.php');
