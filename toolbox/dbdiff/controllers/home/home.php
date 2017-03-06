<?php
namespace toolbox;
class home_controller {

    static function setup(){

    }

    function __construct(){

        router::get()->toInternal('homepage');

    }



}