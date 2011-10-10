<?php


class CacheableData extends Cacheable {
	
	public function __construct(array $aConfig) {
		parent::__construct($aConfig);
	}

	public static function create(array $aConfig = array(), array $aInstances = array(), $nExpiration = null) {
		$sObject = get_called_class();
		return self::createObject($sObject, $aConfig, $aInstances, $nExpiration);
	}
	
	public static function createObject($sObject, array $aConfig = array(), array $aInstances = array(), $nExpiration = null, $sIncludeFile = null, $sExtends = null, array $aImplements = array()) {
		$sIdentifier = 'DATA_' . $sObject . '_' . md5(serialize($aConfig));
		if(!$nExpiration) $nExpiration = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		return parent::_create($sObject, array($aConfig), $aInstances, $sIncludeFile, $sExtends === null ? 'CacheableData' : $sExtends, $aImplements, 'DATA_' . $sIdentifier, time() + $nExpiration);		
	}
}

?>