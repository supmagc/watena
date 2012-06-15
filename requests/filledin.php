<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
require_plugin('ToeVla');

$oConnection = DatabaseManager::getConnection('toevladmin');
$oTable = $oConnection->getTable('festival','!ID');

$oStatement = $oTable->select('0');
while(($oData = $oStatement->fetchObject()) !== false) {
	$sFormat = isset($_GET['csv']) ? '%1$s, %2$s, active:%4$d, uploaded:%5$d, http://www.flandersisafestival.com/iframe/%3$s, http://www.flandersisafestival.com/festival/download/%3$s' . "\r\n" : 
		'<p>Festival: <b>%1$s</b> (<a href="%2$s">%2$s</a>) [active:%4$d] - [uploaded:%5$d]<br />
		&nbsp;&nbsp;<a href="http://www.flandersisafestival.com/iframe/%3$s">http://www.flandersisafestival.com/iframe/%3$s</a><br />
		&nbsp;&nbsp;<a href="http://www.flandersisafestival.com/festival/download/%3$s">http://www.flandersisafestival.com/festival/download/%3$s</a></p>';
	printf($sFormat, $oData->name, $oData->website, $oData->hash, $oData->active ? 1 : 0, $oData->logoFilename ? 1 : 0);
}

?>