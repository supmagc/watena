<?php
require_plugin('DatabaseManager');

class RequestModel extends Model {
	
	public function getConfigData() {
		$aLocal = explode_trim('/', $this->getWatena()->getMapping()->getLocal());
		if(count($aLocal) > 2) {
			$nId = $aLocal[2];
			$sType = $aLocal[1];
			$sKey = "TOEVLA.$sType.$nId";
			return $this->getWatena()->getCache()->retrieve($sKey, array($this, 'getData'), 60 * 60 * 24, array($nId, $sType));
		}
	}
	
	public function getData($nId, $sType) {
		$oStatement = DatabaseManager::getConnection('toevla')->getTable('festival')->select($nId);
		if($oStatement->rowCount() > 0) {
			$oFestivalData = $oStatement->fetch(PDO::FETCH_ASSOC);
			if($sType == 'picasa' && $oFestivalData['picasa']) {
				$oRequest = new WebRequest('http://picasaweb.google.com/data/feed/api/'.$oFestivalData['picasa'].'?access=public&alt=json&kind=photo', 'GET');
				$oResponse = $oRequest->send();
				$aData = json_decode($oResponse->getContent(), true);
				$aUrls = array();
				$aEntries = array_value($aData, array('feed', 'entry'), array());
				foreach($aEntries as $aEntry) {
					$sType = array_value($aEntry, array('content', 'type'));
					if($sType === 'image/jpeg' || $sType == 'image/png') {
						$aUrls []= array_value($aEntry, array('content', 'src'));
					}
				}
				return $aUrls;
			}
			if($sType == 'flickr' && $oFestivalData['flickr']) {
				$oRequest = new WebRequest('http://api.flickr.com/services/feeds/photoset.gne?'.$oFestivalData['flickr'].'&lang=en-us&format=php_serial', 'GET');
				$oResponse = $oRequest->send();
				$aUrls = array();
				$aData = unserialize($oResponse->getContent());
				if(isset($aData['items'])) {
					foreach($aData['items'] as $aItem) {
						if(isset($aItem['photo_url']))
							$aUrls []= $aItem['photo_url'];
					}
				}
				return $aUrls;
			}
		}
		return array();
	}
}

?>