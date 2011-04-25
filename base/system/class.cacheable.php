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
	
	protected static function load($sObject, $sExtends, $aParams, $sIdentifier, $nExpiration) {
		$sIdentifier = md5($sIdentifier);
		$oCache = parent::getWatena()->getCache();
		$nCacheExp = $oCache->get("CACHE_{$sIdentifier}_EXPIRATION", 0);
		
		$oObj = null;		
		if($nExpiration > $nCacheExp) {
			try {
				list($oObject, $oRequirements) = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, $aParams, null, $sExtends, null);
				$oCache->set("CACHE_{$sIdentifier}_EXPIRATION", $mData);
				$oCache->set("CACHE_{$sIdentifier}_REQUIREMENTS", $oRequirements);
				$oCache->set("CACHE_{$sIdentifier}_OBJECT", $oObject);
			}
            catch(WatCeption $e) {
				throw new WatCeption('An exception occured while loading the required object.', array('object' => $sObject, 'file' => $sFilename), $this, $e);
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
	
	/*
	public static final function create($sObject, array $aConfig = array(), $sIdentifier = null, $nExpirationSec = Cacheable::EXP_DEFAULT, $sIncludeFile = null, $sExtends = null, $sImplements = null, $nTimestamp = null) {
		// Generate an identifier if none is given
		if(!$sIdentifier) $sIdentifier = $sObject . count($aPermanentConfig) . implode('', array_keys($aPermanentConfig)) . implode('', array_values($aPermanentConfig));
		else $sIdentifier = $sObject . $sIdentifier;
		$sIdentifier = md5($sIdentifier);

		// Set expiration if a predefined key is given
		$bRefresh = false;
		if($nExpirationSec == Cacheable::EXP_DEFAULT) {
			$nExpirationSec = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		}
		else if($nExpirationSec == Cacheable::EXP_NEVER) {
			$nExpirationSec = 0;
		}
		else if($nExpirationSec == Cacheable::EXP_REFRESH) {
			$nExpirationSec = 0;
			$bRefresh = true;
		}
		
		// When a timestamp is set check it
		if($nTimestamp) {
			$nPrevious = parent::getWatena()->getCache()->get($sIdentifier . '_STAMP', 0);
			if($nTimestamp > $nPrevious) {
				$bRefresh = true;
				parent::getWatena()->getCache()->set($sIdentifier . '_STAMP', $nTimestamp);
			}
		}
		
		// Retrieve the object from the cache
		list($aIncludes, $aExtensionLoads, $aPluginLoads, $sObject) = parent::getWatena()->getCache()->retrieve(
			$sIdentifier, 
			array(parent::getWatena()->getContext(), 'loadClass'), 
			$nExpirationSec, 
			array($sObject, $aConfig, $sIncludeFile, $sExtends, $sImplements, parent::getWatena()->getContext()),
			$bRefresh);
			
		// Check all the returnvalues, and load all dependencies
		foreach($aIncludes as $sInclude) require_once($sInclude);
		foreach($aExtensionLoads as $sExtension) dl($sExtension);
		foreach($aPluginLoads as $sPlugin) parent::getWatena()->getContext()->loadPlugin($sPlugin);
		
		// If all succeeded, unserialize the object and return it
		return unserialize($sObject);
	}
	 * 
	 */
}

?>