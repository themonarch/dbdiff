<?php
echo $_GET['callback'] . '({"html":';
ob_start();
$this->renderViews('json-html');
$html = ob_get_clean();
echo json_encode($html);
echo '})';
