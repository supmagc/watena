<?php
include '../base/watena.php';
WatenaLoader::init();

ini_set('memory_limit', '4400M');

$sData = file_get_contents('test.txt');
dump($sData);
$aData = json_decode($sData, true, 1000000000);
dump($aData);
?>