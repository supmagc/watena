<?php
define('NMVC', true);
include '../base/watena.php';

require_plugin('DatabaseManager');
$oConnection = DatabaseManager::getConnection('toevla');
$oTable = $oConnection->getMultiTable('festival', array('artists', 'artists'), 'OR');

$oTable->update(array('artists' => '§;§;§;§;§;§;§;§;§;§;'), array(null, ''));

/*
$mails = array(
	'jelle@tomo-design.be',
	'jeroen@jeroenvr.be',
	'jelle@grin.be'
);

foreach($mails as $sMail) {
	$oMail = new Mail();
	$oMail->setTo($sMail);
	$oMail->setCc('supmagc@gmail.com', 'Jelle Voet');
	$oMail->setSubject('TestMail');
	$oMail->setContentText('Did you receive this mail ?');
	$oMail->setFrom('info@flandersisafestival.com', 'Flanders Is A Festival');
	$oMail->send();
}
*/
?>