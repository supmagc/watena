<?php

class CacheableFile extends Cacheable {
	
	private $m_sFilename;
	private $m_sFilepath;
	
	protected function __construct($sFilename, $sFilepath, array $aConfig = array()) {
		$this->m_sFilename = $sFilename;
		$this->m_sFilepath = $sFilepath;
	}
	
	public function getFilename() {
		
	}
	
	public function getFileData() {
		
	}
	
	public function printFileData() {
		
	}
	
	public static function load($sFilename, array $aConfig = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		$sObject = get_called_class();
		$oCache = parent::getWatena()->getCache();
		$sFilepath = parent::getWatena()->getPath($sFilename);
		if($sFilepath === false) throw new Exception("Cachefile does not exist: $sFilename");
		
		$sIdentifier = md5($sFilename);
		$nCacheExp = $oCache->get("CACHEFILE_{$sIdentifier}_EXPIRATION", 0);
		$nFileExp = filemtime($sFilepath);
		
		$oObj = null;		
		if($nFileExp > $nCacheExp) {
			parent::getWatena()->getContext()->checkRequirements($aRequirements)
			
			parent::getWatena()->getContext()->loadClass($sObject, $aConfig)
			
			$oObj = new $sObject($sFilename, $sFilepath, $aConfig);*/
		}
		else {
			
		}
	}
}

?>