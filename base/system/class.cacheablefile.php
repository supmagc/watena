<?php

class CacheableFile extends Cacheable {
	
	private $m_sFileName;
	private $m_sFilePath;
	
	public final function __construct($sFileName, $sFilePath, array $aConfig = array()) {
		$this->m_sFileName = $sFileName;
		$this->m_sFilePath = $sFilePath;
		parent::__construct($aConfig);
	}
	
	public function getFileName() {
		return $this->m_sFileName;
	}
	
	public function getFilePath() {
		return $this->m_sFilePath;
	}
	
	public function getFileData() {
		return file_get_contents($this->m_sFilePath);
	}
	
	public function printFileData() {
		echo file_get_contents($this->m_sFilePath);		
	}
	
	public static function create($sFile, array $aConfig = array(), array $aInstances = array()) {
		$sObject = get_called_class();
		return self::createObject($sObject, $sFile, $aConfig, $aInstances);
	}
	
	public static function createObject($sObject, $sFile, array $aConfig = array(), array $aInstances = array(), $sIncludeFile = null, $sExtends = null, array $aImplements = array()) {
		$sFilePath = parent::getWatena()->getPath($sFile);
		if($sFilePath === false || !is_file($sFilePath)) throw new WatCeption('Cachefile does not exist: {file}', array('file' => $sFile));
		return parent::_create($sObject, array($sFile, $sFilePath, $aConfig), $aInstances, $sIncludeFile, $sExtends === null ? 'CacheableFile' : $sExtends, $aImplements, 'FILE_' . $sObject, filemtime($sFilePath));
	}
}

?>