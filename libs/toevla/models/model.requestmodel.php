<?php
require_plugin('DatabaseManager');

class RequestModel extends Model {
	
	public function getConfigData() {
		$aLocal = explode_trim('/', $this->getWatena()->getMapping()->getLocal());
		$sType = $aLocal[1];
		$sMethod = null;
		$sQuery = null;
		if(count($aLocal) > 2) {
			if(is_numeric($aLocal[2])) {
				$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival')->select($aLocal[2]);
				if(($aFestivalData = $oStatement->fetch(PDO::FETCH_ASSOC)) !== false) {
					if($sType == 'picasa' && $aFestivalData['picasa']) {
						$sMethod = 'getPicasaData';
						$aPicasa = unserialize($aFestivalData['picasa']);
						if($aPicasa['type'] == 'default') $sQuery = 'http://picasaweb.google.com/data/feed/api/'.$aPicasa['url'].'?access=public&alt=json&kind=photo';
					}
					if($sType == 'flickr' && $aFestivalData['flickr']) {
						$sMethod = 'getFlickrData';
						$aFlickr = unserialize($aFestivalData['flickr']);
						if($aFlickr['type'] == 'set') $sQuery = 'http://api.flickr.com/services/feeds/photoset.gne?' . $aFlickr['url'] . '&format=php_serial';
						if($aFlickr['type'] == 'public') $sQuery = 'http://api.flickr.com/services/feeds/photos_public.gne?' . $aFlickr['url'] . '&format=php_serial';
						if($aFlickr['type'] == 'faves') $sQuery = 'http://api.flickr.com/services/feeds/photos_faves.gne?' . $aFlickr['url'] . '&format=php_serial';
					}
				}
			}
			else {				
				$sPath = PATH_LIBS . '/toevla/listings/' . $aLocal[2] . '.txt';
				if(is_readable($sPath)) {
					return explode_trim("\n", Encoding::replace("\r", "\n", file_get_contents($sPath)));
				}
			}
		}
		else if(isset($_GET['query'])) {
			$sQuery = $_GET[query];
			if($sType == 'picasa') $sMethod = 'getPicasaData';
			if($sType == 'flickr') $sMethod = 'getFlickrData';
		}

		if($sMethod && $sQuery) {
			$sKey = "TOEVLA.$sMethod.$sQuery";
			return $this->getWatena()->getCache()->retrieve($sKey, array($this, $sMethod), 60 * 60 * 24, array($sQuery));
		}
		else {
			return array();
		}
	}
	
	public function getPicasaData($sUrl) {
		$oRequest = new WebRequest($sUrl, 'GET');
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
	
	public function getFlickrData($sUrl) {
		$oRequest = new WebRequest($sUrl, 'GET');
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
}

?>