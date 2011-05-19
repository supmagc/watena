<?php

class CacheableDirectory extends Cacheable {
	
	private $m_sDirectoryName;
	private $m_sDirectoryPath;
	
	public final function CacheableDirectory($sDirectoryName, $sDirectoryPath, array $aConfig = array()) {
		$this->m_sDirectoryName = $sDirectoryName;
		$this->m_sDirectoryPath = $sDirectoryPath;
		parent::Cacheable($aConfig);
	}
	
	public function getDirectoryName() {
		return $this->m_sDirectoryName;
	}
	
	public function getDirectoryPath() {
		return $this->m_sDirectoryPath;
	}
	
	public static function create($sDirectoryName, array $aConfig = array()) {
		$sObject = get_called_class();
		return self::createObject($sObject, $sDirectoryName, $aConfig);
	}
	
	public static function createObject($sObject, $sDirectoryName, array $aConfig = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		$sDirectoryPath = parent::getWatena()->getPath($sDirectoryName);
		if($sDirectoryPath === false) throw new WatCeption('CacheableDirectory does not exist.', array('name' => $sDirectoryName, 'path' => $sDirectoryPath));
		if(!is_dir($sDirectoryPath)) throw new WatCeption('CacheableDirectory is no directory.', array('name' => $sDirectoryName, 'path' => $sDirectoryPath));
		return parent::_create($sObject, array($sDirectoryname, $sDirectorypath, $aConfig), $sIncludeFile, $sExtends === null ? 'CacheableDirectory' : $sExtends, $sImplements, 'DIRECTORY_' . $sObject, filemtime($sDirectorypath));
	}	
}
?>
