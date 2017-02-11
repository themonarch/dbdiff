<?php
namespace toolbox;
page::get()->renderViews();

//signal the shutdown function to not report
//any exceptions as we already handled them if execution got to this point
//(either they were caught, or our exception handler already did)
toolbox::$error_handled = true;
