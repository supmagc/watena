<?php
require_plugin('DatabaseManager');

class RequestModel extends Model {
	
	public function getConfigData() {
		$aLocal = explode_trim('/', $this->getWatena()->getMapping()->getLocal());
		$nId = null;
		$qType = null;
		$sQuery = null;
		if(count($aLocal) > 2) {
			$nId = $aLocal[2];
			$sType = $aLocal[1];
		}
		if(count($aLocal) > 1 && isset($_GET['query'])) {
			$sQuery = $_GET['query'];
			$sType = $aLocal[1];
		}
		
		if($aLocal[1] == 'flush') {
				$this->getWatena()->getCache()->delete("TOEVLA.flickr.$nId.$sQuery");
				$this->getWatena()->getCache()->delete("TOEVLA.picasa.$nId.$sQuery");
		} 
		else {
			$sKey = "TOEVLA.$sType.$nId.$sQuery";
			return $this->getWatena()->getCache()->retrieve($sKey, array($this, 'getData'), 60 * 60 * 24, array($nId, $sQuery, $sType));
		}
	}
	
	public function getData($sId, $sQuery, $sType) {
		if(!$sQuery) {
			$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival')->select($sId);
			$aFestivalData = $oStatement->rowCount() > 0 ? $oStatement->fetch(PDO::FETCH_ASSOC) : array();
		}
		if($sType == 'picasa' && ($sQuery || $aFestivalData['picasa'])) {
			$oRequest = new WebRequest('http://picasaweb.google.com/data/feed/api/'.($sQuery ?: $aFestivalData['picasa']).'?access=public&alt=json&kind=photo', 'GET');
			$oResponse = $oRequest->send();
			$aData = json_decode($oResponse->getContent(), true);
			$aUrls = array();
			$aEntries = array_value($aData, array('feed', 'entry'), array());
			foreach($aEntries as $aEntry) {
				$sType = array_value($aEntry, array('content', 'type'));
				if($sType === 'image/jpeg' || $sType == 'image/png') {
					$aUrls []= array_value($aEntry, array('media$group', 'media$thumbnail', 2, 'url'));
				}
			}
			return $aUrls;
		}
		if($sType == 'flickr' && ($sQuery || $aFestivalData['flickr'])) {
			$oRequest = new WebRequest('http://api.flickr.com/services/feeds/photoset.gne?'.($sQuery ?: $aFestivalData['flickr']).'&lang=en-us&format=php_serial', 'GET');
			$oResponse = $oRequest->send();
			$aUrls = array();
			$aData = unserialize($oResponse->getContent());
			if(isset($aData['items'])) {
				foreach($aData['items'] as $aItem) {
					if(isset($aItem['m_url']))
						$aUrls []= $aItem['m_url'];
				}
			}
			return $aUrls;
		}			
		return array();
	}
}

?>