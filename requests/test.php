<?php
include '../base/watena.php';
WatenaLoader::init();

$a = 10;
$b = &$a;
echo "$a - $b<br />";
$b += 10;
echo "$a - $b<br />";
$a += 10;
echo "$a - $b<br />";
?>