<?php

class CacheableFiles extends Cacheable {
	
	private $m_aFileNames;
	private $m_aFilePaths;
	
	public final function __construct(array $aFileNames, array $aFilePaths, array $aConfig = array()) {
		$this->m_aFileNames = $aFileNames;
		$this->m_aFilePaths = $aFilePaths;
		parent::__construct($aConfig);
	}
	
	public function getFileNames() {
		return $this->m_aFileNames;
	}
	
	public function getFileName($nIndex) {
		if($nIndex >= 0 && $nIndex < count($this->m_aFileNames)) {
			return $this->m_aFileNames[$nIndex];
		}
		else {
			$this->getLogger()->warning('Invalid index \'{index}\' to retrieve filename from CacheableFiles.', array('index' => $nIndex));
			return false;
		}
	}
	
	public function getFilePaths() {
		return $this->m_aFilePaths;
	}
	
	public function getFilePath($nIndex) {
		if($nIndex >= 0 && $nIndex < count($this->m_aFilePaths)) {
			return $this->m_aFilePath[$nIndex];
		}
		else {
			$this->getLogger()->warning('Invalid index \'{index}\' to retrieve filepath from CacheableFiles.', array('index' => $nIndex));
			return false;
		}
	}
	
	public function getFileData($nIndex) {
		if(($sTemp = $this->getFilePath($nIndex)) !== false)
			return file_get_contents($sTemp);
		else
			return false;
	}
	
	public function printFileData($nIndex) {
		if(($sTemp = $this->getFilePath($nIndex)) !== false)
			echo file_get_contents($sTemp);		
	}
	
	public static function create($sFile, array $aConfig = array(), array $aInstances = array()) {
		$sObject = get_called_class();
		return self::createObject($sObject, $sFile, $aConfig, $aInstances);
	}
	
	public static function createObject($sObject, array $aFiles, array $aConfig = array(), array $aInstances = array(), $sIncludeFile = null, $sExtends = null, array $aImplements = array()) {
		$aFileNames = array();
		$aFilePaths = array();
		$nExpiration = 0;
		foreach($aFiles as $sFile) {
			$sFilePath = parent::getWatena()->getPath($sFile);
			if($sFilePath === false || !is_file($sFilePath)) throw new WatCeption('Cachefile does not exist: {file}', array('file' => $sFile));
			$aFileNames []= $sFile;
			$aFilePaths []= $sFileName;
			$nExpiration = max($nExpiration, filemtime($sFilePath));
		}
		return parent::_create($sObject, array($aFileNames, $aFilePaths, $aConfig), $aInstances, null, $sExtends === null ? 'CacheableFiles' : $sExtends, $aImplements, 'FILES_' . $sObject, $nExpiration);
	}
}

?>