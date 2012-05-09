<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
$oConnection = DatabaseManager::getConnection('toevladmin');
$oTable = $oConnection->getTable('festival');
dump($oTable->select('13')->fetchObject());

?>