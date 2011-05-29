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
	
	public static function create($sFileName, array $aConfig = array()) {
		$sObject = get_called_class();
		return self::createObject($sObject, $sFileName, $aConfig);
	}
	
	public static function createObject($sObject, $sFileName, array $aConfig = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		$sFilePath = parent::getWatena()->getPath($sFileName);
		if($sFilePath === false || !is_file($sFilePath)) throw new WatCeption('Cachefile does not exist.', array('file' => $sFileName));
		return parent::_create($sObject, array($sFileName, $sFilePath, $aConfig), $sIncludeFile, $sExtends === null ? 'CacheableFile' : $sExtends, $sImplements, 'FILE_' . $sObject, filemtime($sFilePath));
	}
}

?>