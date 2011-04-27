<?php


class CacheableData extends Cacheable {
	
	public function CacheableData(array $aConfig) {
		parent::__construct($aConfig);
	}

	public static function create(array $aConfig = array(), $nExpiration = null) {
		$sObject = get_called_class();
		return self::createObject($sObject, $aConfig, $nExpiration);
	}
	
	public static function createObject($sObject, array $aConfig = array(), $nExpiration = null) {
		$sIdentifier = 'DATA_' . $sObject . '_' . md5(serialize($aConfig));
		if(!$nExpiration) $nExpiration = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		return parent::_create($sObject, 'CacheableData', array($aConfig), $sIdentifier, $nExpiration);		
	}
}

?>