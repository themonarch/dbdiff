<?php
namespace toolbox;
 /**
 * 'This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.'
 */
chdir(__DIR__);


require '../toolbox/start.php';

/**
 * This enables routing to determine which logic/contents
 * to serve based on the uri path.
 */
toolbox::get('dbdiff')->start();
