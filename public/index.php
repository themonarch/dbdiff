<?php
namespace toolbox;
 /**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the public root now.
 */
chdir(__DIR__);


require '../toolbox/start.php';

/**
 * Start our app with routing enabled. This will route to the
 * proper controller in toolbox/YOUR_APP/controllers/... based
 * the REQUEST_URI, with any modifiers applied along the way.
 */
toolbox::get('dbdiff')->start();
