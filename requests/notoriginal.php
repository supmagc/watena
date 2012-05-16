<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
require_plugin('ToeVla');

$oConnection = DatabaseManager::getConnection('toevladmin');
$oTable = $oConnection->getMultiTable('festival', array('fmiv', 'fiaf'), 'AND');

$oStatement = $oTable->select(array(0, 0));
while(($oData = $oStatement->fetchObject()) !== false) {
	printf('Festival: %1$s (<a href="%2$s">%2$s</a>)<br />', $oData->name, $oData->website);
}

?>