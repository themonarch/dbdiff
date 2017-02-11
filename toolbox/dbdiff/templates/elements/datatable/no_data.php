<?php
namespace toolbox;
?>
<div class="catchall-border" style="background-color: #D0D0D0;"></div>
<div class="catchall padding-3"></div>
<?php
page::create()
	->set('notice', 'No data found!')
	->addView('elements/notice.php')->renderViews();
?>
<div class="catchall padding-3"></div>