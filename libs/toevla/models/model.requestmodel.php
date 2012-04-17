<?php
require_plugin('DatabaseManager');

class RequestModel extends Model {
	
	public function getConfigData() {
		$nId = $_GET['ID'];
		$sType = Encoding::substring($this->getWatena()->getMapping()->getLocal(), 9);
		$sKey = "TOEVLA.$sType.$nId";
		return $this->getWatena()->getCache()->retrieve($sKey, array($this, 'getData'), 60 * 60 * 24, array($nId, $sType));
	}
	
	public function getData($nId, $sType) {
		$aData = DatabaseManager::getConnection('toevla')->getTable('festival')->select($nId)->fetch(PDO::FETCH_ASSOC);
		if($sType == 'picasa') {
			$oRequest = new WebRequest('http://picasaweb.google.com/data/feed/api/user/105238180871871114280/?access=public&test=' . time(), 'GET');
			$oRequest->addHeader("User-Agent", $_SERVER['HTTP_USER_AGENT']);
			$oResponse = $oRequest->send();
			echo '<pre>';
			print_r($oRequest);
			print_r($oResponse);
			echo file_get_contents('http://picasaweb.google.com/data/feed/api/user/105238180871871114280/?access=public');
			echo '</pre>';
		}
		if($sType == 'flickr') {
			$oRequest = new WebRequest('http://api.flickr.com/services/feeds/photoset.gne?set=307262&nsid=14846397@N00&lang=en-us', 'GET');
			$oResponse = $oRequest->send();
			print_r($oResponse);
		}
		return array();
	}
}

?>