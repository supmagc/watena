<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');

dump(Encoding::length('a:3:{s:6:"source";s:54:"http://picasaweb.google.com/data/feed/api/user/105238180871871114280/albumid/5663284935360263649?access=public&alt=json&kind=photo";s:4:"type";s:7:"default";s:3:"url";s:54:"user/105238180871871114280/albumid/5663284935360263649";}'));

$oTable = DatabaseManager::getConnection('toevla')->getTable('festival');
$oStatement = $oTable->select();
foreach($oStatement as $aRow) {
	$aFlickr = unserialize($aRow['flickr']);
	$aFlickr['source'] = $aFlickr['url'];
	$oTable->update(array(
		'flickr' => serialize($aFlickr)
	), $aRow['ID']);
}
?>