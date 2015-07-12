<?php namespace Watena\Core;

class CacheableData extends Cacheable {

	public static function create(array $aMembers, array $aConfig = array()) {
		$oLoader = new CacheLoader(get_called_class(), $aMembers);
		return $oLoader->get($aConfig);
	}
	
	/*public static function includeAndCreate($sIncludeFileName, $sClassName, array $aMembers = array(), array $aConfig) {
		$sIncludeFilePath = parent::getWatena()->getPath($sIncludeFileName);
		if(!$sIncludeFilePath || !is_file($sIncludeFilePath) || !is_readable($sIncludeFilePath)) {
			Logger::getInstance(get_called_class())->error('CacheableData cannot include the required file \'{file}\' to load \'{class}\'.', array('file' => $sIncludeFileName, 'class' => $sClassName));
		}
		else {
			include_safe($sIncludeFilePath);
		}
		$oLoader = new CacheLoader($sClassName, $aMembers, get_called_class());
		$oLoader->addPathDependency($sIncludeFilePath);
		return $oLoader->get($aConfig);
	}*/
}
