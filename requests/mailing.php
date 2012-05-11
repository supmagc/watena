<?php
define('MAILCOUNT', 2);
define('NMVC', true);
include '../base/watena.php';

require_plugin("DatabaseManager");
$oConnection = DatabaseManager::getConnection('toevla');
$oTable = $oConnection->getTable('festival');
$oStatement = $oTable->select();

while(($oData = $oStatement->fetchObject()) !== false) {
	if($oData->adminMail && $oData->adminName && $oData->mailCount < MAILCOUNT) {
		$oMail = new Mail();
		$oMail->setTo($oData->adminMail, $oData->adminName);
		$oMail->setFrom('info@flandersisafestival.com', 'Flanders Is A Festival');
		$oMail->addCc('jos.pyck@toerismevlaanderen.be', 'Jos Pyck');
		$oMail->addCc('wim@grin.be', 'Wim Wouters');
		$oMail->setSubject('Festival editor: Flanders is a Festival');
		$oMail->setReceipt(true);
		$sContent = file_get_contents(dirname(__FILE__) . '/mailing.html');
		$sContent = Encoding::replace(array('{name}', '{linkExe}', '{linkPreview}'), array($oData->adminName, 'http://flandersisafestival.com/festival/download/' . $oData->hash, 'http://flandersisafestival.com/iframe/' . $oData->hash), $sContent);
		$oMail->setContentHtml($sContent);
		$oMail->convertHtmlToText();
		
		echo '<pre>';
		echo $oMail->get();
		echo '</pre>';
		
		if(isset($_GET['action'])) {
			if($_GET['action'] == 'test') {
				$oMail->setTo('supmagc@gmail.com', 'Jelle Voet');
				$oMail->setCc('jelle@tomo-design.be', 'Jelle Voet');
				$oMail->setBcc('jelle@grin.be', 'Jelle Voet');
			}
			if($_GET['action'] == 'send' || $_GET['action'] == 'test') {
				$oMail->send();
				if($_GET['action'] == 'send')
					$oTable->update(array('mailCount' => MAILCOUNT), $oData->ID);
			}
			if($_GET['action'] == 'test')
				break;
		}
	}
}

?>