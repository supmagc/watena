<?php
require_plugin('DatabaseManager');

class RequestModel extends Model {
	
	public function getConfigData() {
		$aLocal = explode_trim('/', $this->getWatena()->getMapping()->getLocal());
		$nId = null;
		$qType = null;
		$sQuery = null;
		$sType = $aLocal[1];
		if(count($aLocal) > 2) {
			$nId = $aLocal[2];
		}
		if(count($aLocal) > 1 && isset($_GET['query'])) {
			$sQuery = $_GET['query'];
		}
		
		if($sType == 'flush') {
				$this->getWatena()->getCache()->delete("TOEVLA.flickr.$nId.$sQuery");
				$this->getWatena()->getCache()->delete("TOEVLA.picasa.$nId.$sQuery");
		} 
		else if($sType == 'picasa' || $sType == 'flickr') {
			$sKey = "TOEVLA.$sType.$nId.$sQuery";
			return $this->getWatena()->getCache()->retrieve($sKey, array($this, 'getData'), 60 * 60 * 24, array($nId, $sQuery, $sType));
		}
		else if(file_exists(PATH_LIBS . '/toevla/listings/' . $sType . '.txt')) {
			return explode_trim("\n", Encoding::replace("\r", "", file_get_contents(PATH_LIBS . '/toevla/listings/' . $sType . '.txt')));
		}
		else {
			return array();
		}
	}
	
	public function getData($sId, $sQuery, $sType) {
		if(!$sQuery) {
			$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival')->select($sId);
			$aFestivalData = $oStatement->rowCount() > 0 ? $oStatement->fetch(PDO::FETCH_ASSOC) : array();
		}
		if($sType == 'picasa' && ($sQuery || $aFestivalData['picasa'])) {
			if($sQuery) {
				$oRequest = new WebRequest($sQuery, 'GET');
			}
			else {
				$oRequest = new WebRequest('http://picasaweb.google.com/data/feed/api/'.$aFestivalData['picasa'].'?access=public&alt=json&kind=photo', 'GET');
			}
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
			if($sQuery) {
				$oRequest = new WebRequest($sQuery, 'GET');
			}
			else {
				parse_str($aFestivalData['flickr'], $aParams);
				if(!isset($aParams['set']))
					$oRequest = new WebRequest('http://api.flickr.com/services/feeds/photos_public.gne?'.$aFestivalData['flickr'].'&lang=en-us&format=php_serial', 'GET');
				else
					$oRequest = new WebRequest('http://api.flickr.com/services/feeds/photoset.gne?'.$aFestivalData['flickr'].'&lang=en-us&format=php_serial', 'GET');
			}
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