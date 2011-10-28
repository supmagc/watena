<?php

class CacheableDirectory extends Cacheable {
	
	private $m_sDirectoryName;
	private $m_sDirectoryPath;
	
	public final function __construct($sDirectoryName, $sDirectoryPath, array $aConfig = array()) {
		$this->m_sDirectoryName = $sDirectoryName;
		$this->m_sDirectoryPath = $sDirectoryPath;
		parent::__construct($aConfig);
	}
	
	public function getDirectoryName() {
		return $this->m_sDirectoryName;
	}
	
	public function getDirectoryPath() {
		return $this->m_sDirectoryPath;
	}
	
	public function getFiles($sExtention = null, $bFullPath = false, $sRegex = null) {
		$hDir = opendir($this->m_sDirectoryPath);
		$aFiles = array();
		while(($sEntry = readdir($hDir)) !== false) {
			$sPath = realpath($this->m_sDirectoryPath . '/' . $sEntry);
			if(is_file($sPath) && ($sExtention === null || Encoding::endsWith($sEntry, ".$sExtention", true)) && ($sRegex === null || Encoding::regMatch($sRegex, $sEntry, 'i')))
				$aFiles []= $bFullPath ? $sPath : $sEntry;
		}
		closedir($hDir);
		return $aFiles;
	}
	
	public function hasFile($sName) {
		return is_file($this->m_sDirectoryPath . '/' . $sName);
	}
	
	public function getDirectories($bFullpath = false) {
		$hDir = opendir($this->m_sDirectoryPath);
		$aEntries = readdir($hDir);
		closedir($hDir);
		$aDirs = array();
		if(is_array($aEntries)) {
			foreach($aEntries as $sEntry) {
				$sPath = realpath($this->m_sDirectoryPath . '/' . $sEntry);
				if(is_dir($sPath))
					$aDirs []= $bFullPath ? $sPath : $sEntry;
			}
		}
		return $aDirs;	
	}
	
	public function hasDirectory($sName) {
		return is_dir($this->m_sDirectoryPath . '/' . $sName);
	}
	
	public static function create($sDirectoryName, array $aConfig = array(), array $aInstances = array()) {
		$sObject = get_called_class();
		return self::createObject($sObject, $sDirectoryName, $aConfig, $aInstances);
	}
	
	public static function createObject($sObject, $sDirectoryName, array $aConfig = array(), array $aInstances = array(), $sIncludeFile = null, $sExtends = null, array $aImplements = array()) {
		$sDirectoryPath = parent::getWatena()->getPath($sDirectoryName);
		if($sDirectoryPath === false || !is_dir($sDirectoryPath)) throw new WatCeption('CacheDirectory does not exist.', array('Directory' => $sDirectoryName));
		return parent::_create($sObject, array($sDirectoryName, $sDirectoryPath, $aConfig), $aInstances, $sIncludeFile, $sExtends === null ? 'CacheableDirectory' : $sExtends, $aImplements, 'DIRECTORY_' . $sObject, filemtime($sDirectoryPath));
	}
}

?>