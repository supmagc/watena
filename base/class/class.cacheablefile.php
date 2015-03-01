<?php

class CacheableFile extends Cacheable {
	
	private $m_sFileName;
	private $m_sFilePath;
	
	public function getFileTimestamp() {
		return filechangetime($this->m_sFilePath);
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
	
	public function includeFile() {
		return include_safe($this->m_sFilePath);
	}
	
	public static function create($sFileName, array $aMembers = array(), array $aConfig = array()) {
		$oLoader = new CacheLoaderFile(get_called_class(), $sFileName, $aMembers);
		return $oLoader->get($aConfig);
	}
}
