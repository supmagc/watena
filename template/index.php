<?php
require_once 'template.php';
require_once 'templatebuilder.php';
require_once 'templateparser.php';
require_once 'templatereader.php';

ob_start();
$oTemplate = new Template('./source.tpl');
$sContent = ob_get_contents();
ob_end_clean();
echo htmlentities($sContent);

?>