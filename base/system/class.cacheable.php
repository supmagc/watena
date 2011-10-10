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
	
	public function __construct(array $aConfig) {
		parent::__construct($aConfig);
	}
	
	public function getInstance($sKey, $mDefault = null) {
		$sKey = strtoupper($sKey); // Don't use Encoding, since it might not be inited yet
		return isset($this->m_aInstances[$sKey]) ? $this->m_aInstances[$sKey] : $mDefault;
	}
	
	public final function getInstances() {
		return $this->m_aInstances;
	}
	
	protected static function _create($sObject, array $aParams = array(), array $aInstances = array(), $sIncludeFile = null, $sExtends = null, array $aImplements = array(), $sIdentifier = null, $nExpiration = 0, $bUseDependencyCallback = false) {
		$sIdentifier = '' . $sIdentifier . '_' . md5(serialize($aParams));
		$oCache = parent::getWatena()->getCache();
		$nCacheExp = $oCache->get("W_CACHE_{$sIdentifier}_EXPIRATION", 0);
		$oRequirements = $oCache->get("W_CACHE_{$sIdentifier}_REQUIREMENTS", null);
		$oObject = $oCache->get("W_CACHE_{$sIdentifier}_OBJECT", null);
		$aInstances = array_change_key_case($aInstances, CASE_UPPER);
		
		if($nExpiration > $nCacheExp || !$oRequirements || !$oObject) {
			try {
				list($oObject, $oRequirements) = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, $aParams, $sIncludeFile, $sExtends, $aImplements, $bUseDependencyCallback);
				if($oRequirements->IsSucces()) {
					$oObject->m_aInstances = $aInstances;
					$oObject->init();
					$oObject->m_aInstances = null;
					$oCache->set("W_CACHE_{$sIdentifier}_EXPIRATION", $nExpiration);
					$oCache->set("W_CACHE_{$sIdentifier}_REQUIREMENTS", $oRequirements);
					$oCache->set("W_CACHE_{$sIdentifier}_OBJECT", serialize($oObject));
					$oObject->m_aInstances = $aInstances;
					$oObject->wakeup();
					return $oObject;
				}
				else {
					throw new WatCeption('The object required for caching does not meets it requirements.', array('object' => $sObject, 'requirements' => $oRequirements), $this);
				}
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
			if($oRequirements->IsSucces()) {
				$oObject = unserialize($oObject);
				$oObject->m_aInstances = $aInstances;
				$oObject->wakeup();
				return $oObject;
			}
			else {
				throw new WatCeption('A previously loaded and cached object no longer meets it requirements.', array('object' => $sObject, 'requirements' => $oRequirements), $this);
			}
		}		
	}
}

?>