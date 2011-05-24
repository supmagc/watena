<?php

class CacheableFile extends Cacheable {
	
	private $m_sFilename;
	private $m_sFilepath;
	
	public final function CacheableFile($sFilename, $sFilepath, array $aConfig = array()) {
		$this->m_sFilename = $sFilename;
		$this->m_sFilepath = $sFilepath;
		parent::Cacheable($aConfig);
	}
	
	public function getFilename() {
		return $this->m_sFilename;
	}
	
	public function getFilepath() {
		return $this->m_sFilepath;
	}
	
	public function getFileData() {
		return file_get_contents($this->m_sFilepath);
	}
	
	public function printFileData() {
		echo file_get_contents($this->m_sFilepath);		
	}
	
	public static function create($sFilename, array $aConfig = array()) {
		$sObject = get_called_class();
		return self::createObject($sObject, $sFilename, $aConfig);
	}
	
	public static function createObject($sObject, $sFilename, array $aConfig = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		$sFilepath = parent::getWatena()->getPath($sFilename);
		if($sFilepath === false || !is_file($sFilepath)) throw new WatCeption('Cachefile does not exist.', array('file' => $sFilename));
		return parent::_create($sObject, array($sFilename, $sFilepath, $aConfig), $sIncludeFile, $sExtends === null ? 'CacheableFile' : $sExtends, $sImplements, 'FILE_' . $sObject, filemtime($sFilepath));
	}
}

?>