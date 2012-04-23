<?php
define('NMVC', true);
include '../base/watena.php';

$oZipper = new ZipFile(PATH_DATA . '/zipper/build.zip'); 
$oZipper->add('ToeVlaEditor', PATH_DATA . '/editor');
$oZipper->create('ToeVlaEditor/data', "http://flandersisafestival.dev/festival/save
http://flandersisafestival.dev/festival/load
97313657793285f643307c4f3d943389
Blues Peer");
$oZipper->setComment('Some comment !!');

?>