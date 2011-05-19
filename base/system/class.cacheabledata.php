<?php


class CacheableData extends Cacheable {
	
	public function CacheableData(array $aConfig) {
		parent::Cacheable($aConfig);
	}

	public static function create(array $aConfig = array(), $nExpiration = null) {
		$sObject = get_called_class();
		return self::createObject($sObject, $aConfig, $nExpiration);
	}
	
	public static function createObject($sObject, array $aConfig = array(), $nExpiration = null, $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		$sIdentifier = 'DATA_' . $sObject . '_' . md5(serialize($aConfig));
		if(!$nExpiration) $nExpiration = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		return parent::_create($sObject, array($aConfig), $sIncludeFile, $sExtends === null ? 'CacheableData' : $sExtends, $sImplements, 'DATA_' . $sIdentifier, $nExpiration);		
	}
}

?>