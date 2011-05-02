<?php

abstract class Cacheable extends Configurable {
	
	const EXP_DEFAULT = 0;
	const EXP_NEVER = -1;
	const EXP_REFRESH = -2;
	
	/**
	 * This method is called when initting the object and should leave the object in a cacheable/serializeable state.
	 */
	public function init() {}
	
	/**
	 * This method is called when waking the obhect when loading it back from the cache.
	 * For example: creating a database connection should be done at this time.
	 */
	public function wakeup() {}
	
	protected function Cacheable(array $aConfig) {
		parent::__construct($aConfig);
		$this->init();
	}
	
	public final function __wakeup() {
		$this->wakeup();
	}
	
	protected static function _create($sObject, $aParams, $sIncludeFile, $sExtends, $sImplements, $sIdentifier, $nExpiration) {
		$sIdentifier = $sIdentifier . '_' . md5(serialize($aParams));
		$oCache = parent::getWatena()->getCache();
		$nCacheExp = $oCache->get("CACHE_{$sIdentifier}_EXPIRATION", 0);
		
		if($nExpiration > $nCacheExp) {
			try {
				list($oObject, $oRequirements) = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, $aParams, $sIncludeFile, $sExtends, $sImplements);
				$oCache->set("CACHE_{$sIdentifier}_EXPIRATION", $nExpiration);
				$oCache->set("CACHE_{$sIdentifier}_REQUIREMENTS", $oRequirements);
				$oCache->set("CACHE_{$sIdentifier}_OBJECT", $oObject);
				return $oObject;
			}
            catch(WatCeption $e) {
				throw new WatCeption('An exception occured while loading the required object.', array(
					'object' => $sObject,
					'params' => $aParams,
					'includeFile' => $sIncludeFile,
					'extends' => $sExtends,
					'implements' => $sImplements,
					'identifier' => $sIdentifier), null, $e);
            }
		}
		else {
			$oRequirements = $oCache->get("CACHE_{$sIdentifier}_REQUIREMENTS", null);
			if($oRequirements->IsSucces()) {
				$oObject = $oCache->get("CACHE_{$sIdentifier}_OBJECT", null);
				return $oObject;
			}
			else {
				throw new WatCeption('A previously loaded and cached object no longer meets it requirements.', array('object' => $sObject, 'requirements' => $oRequirements), $this);
			}
		}		
	}
}

?>