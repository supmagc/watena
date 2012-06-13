<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
require_plugin('ToeVla');

$oConnection = DatabaseManager::getConnection('toevladmin');
$oTable = $oConnection->getMultiTable('festival', array('logoFilename', 'logoFilename'), 'OR');

$oStatement = $oTable->select(array('', null));
while(($oData = $oStatement->fetchObject()) !== false) {
	printf('Festival: %1$s (<a href="%2$s">%2$s</a>)<br />', $oData->name, $oData->website);
}

?>