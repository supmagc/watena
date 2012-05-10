<?php
define('MAILCOUNT', 0);
define('NMVC', true);
include '../base/watena.php';

require_plugin("DatabaseManager");
$oConnection = DatabaseManager::getConnection('toevla');
$oTable = $oConnection->getTable('festival');
$oStatement = $oTable->select();

while(($oData = $oStatement->fetchObject()) !== false) {
	if($oData->adminMail && $oData->adminName && $oData->mailCount <= MAILCOUNT) {
		$oMail = new Mail();
		$oMail->setTo($oData->adminMail, $oData->adminName);
		$oMail->setFrom('info@flandersisafestival.com', 'Flanders Is A Festival');
		$oMail->setSubject('Festival editor: Flanders is a Festival');
		$sContent = file_get_contents(dirname(__FILE__) . '/mailing.html');
		$sContent = Encoding::replace(array('{name}', '{linkExe}', '{linkPreview}'), array($oData->adminName, new Mapping('/festival/download/' . $oData->hash), new Mapping('/iframe/' . $oData->hash)), $sContent);
		$oMail->setContentHtml($sContent);
		$oMail->convertHtmlToText();
		//$oMail->setContentHtml(null);
		
		echo '<pre>';
		echo $oMail->get();
		echo '</pre>';
		
		if(isset($_GET['action'])) {
			if($_GET['action'] == 'test')
				$oMail->setTo('supmagc@gmail.com', 'Jelle Voet');
			if($_GET['action'] == 'send' || $_GET['action'] == 'test')
				$oMail->send();
			if($_GET['action'] == 'test')
				break;
		}
	}
}

?>