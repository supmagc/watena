<?php


class CacheableData extends Cacheable {

	public static function load(array $aConfig = array(), $nExpiration = null) {
		$sObject = get_called_class();
		$oCache = parent::getWatena()->getCache();
		if(!$nExpiration) $nExpiration = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		
		$sIdentifier = md5($sFilename);
		$nCacheExp = $oCache->get("CACHEDATA_{$sIdentifier}_EXPIRATION", 0);
		
		$oObj = null;		
		if(time() > $nCacheExp) {
			$aIncludes = array();
			try {
				list($oObject, $oRequirements) = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, array($aConfig), $sIncludeFile, $sExtends, $sImplements);
				$oCache->set("CACHEDATA_{$sIdentifier}_EXPIRATION", time() + $nExpiration);
				$oCache->set("CACHEDATA_{$sIdentifier}_REQUIREMENTS", $oRequirements);
				$oCache->set("CACHEDATA_{$sIdentifier}_OBJECT", $oObject);
			}
            catch(WatCeption $e) {
				throw new WatCeption('An exception occured while loading the required object.', array('object' => $sObject, 'file' => $sFilename), $this, $e);
            }
		}
		else {
			$oRequirements = $oCache->get("CACHEDATA_{$sIdentifier}_REQUIREMENTS", null);
			if($oRequirements->IsSucces()) {
				$oObject = $oCache->get("CACHEDATA_{$sIdentifier}_OBJECT", null);
				return $oObject;
			}
			else {
				throw new WatCeption('A previously loaded and cached object no longer meets it requirements.', array('object' => $sObject, 'requirements' => $oRequirements), $this);
			}
		}
	}
}

?>