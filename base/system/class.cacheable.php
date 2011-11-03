<?php

abstract class Cacheable extends Configurable {
	
	/**
	 * This method is called when initting the object and should leave the object in a cacheable/serializeable state.
	 */
	public function init() {}
	
	/**
	 * This method is called when waking the obhect when loading it back from the cache.
	 * For example: creating a database connection should be done at this time.
	 */
	public function wakeup() {}
	
	private $m_aInstances = null;
	
	public function getInstance($sKey, $mDefault = null) {
		$sKey = strtoupper($sKey); // Don't use Encoding, since it might not be inited yet
		return isset($this->m_aInstances[$sKey]) ? $this->m_aInstances[$sKey] : $mDefault;
	}
	
	public final function getInstances() {
		return $this->m_aInstances;
	}
	
	protected static function _create($sObject, array $aParams = array(), array $aInstances = array(), $sIncludeFile = null, $sExtends = null, array $aImplements = array(), $sIdentifier = null, $nExpiration = 0) {
		$sIdentifier = '' . $sIdentifier . '_' . md5(serialize($aParams));
		$oCache = parent::getWatena()->getCache();
		$nCacheExp = $oCache->get("W_CACHE_{$sIdentifier}_EXPIRATION", 0);
		$oObject = $oCache->get("W_CACHE_{$sIdentifier}_OBJECT", null);
		$aInstances = array_change_key_case($aInstances, CASE_UPPER);
		
		if($nExpiration > $nCacheExp || !$oRequirements || !$oObject) {
			try {
				$oObject = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, $aParams, $sIncludeFile, $sExtends, $aImplements);
				$oObject->m_aInstances = $aInstances;
				$oObject->init();
				$oObject->m_aInstances = null;
				$oCache->set("W_CACHE_{$sIdentifier}_EXPIRATION", $nExpiration);
				$oCache->set("W_CACHE_{$sIdentifier}_OBJECT", serialize($oObject));
				$oObject->m_aInstances = $aInstances;
				$oObject->wakeup();
				return $oObject;
			}
            catch(WatCeption $e) {
				throw new WatCeption('An exception occured while loading the required object.', array(
					'object' => $sObject,
					'params' => $aParams,
					'includeFile' => $sIncludeFile,
					'extends' => $sExtends,
					'implements' => $aImplements,
					'identifier' => $sIdentifier), parent::getWatena(), $e);
            }
		}
		else {
			try {
				if($sIncludeFile != null) {
					require_includeonce($sIncludeFile);
				}
				$oObject = unserialize($oObject);
				$oObject->m_aInstances = $aInstances;
				$oObject->wakeup();
				return $oObject;
			}
			catch(Exception $e) {
				throw new WatCeption('A previously loaded and cached object no longer meets it requirements.', array('object' => $sObject, 'requirements' => $oRequirements), $this);
			}
		}		
	}
}

?>