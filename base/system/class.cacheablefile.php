<?php

class CacheableFile extends Cacheable {
	
	private $m_sFilename;
	private $m_sFilepath;
	
	protected function __construct($sFilename, $sFilepath, array $aConfig = array()) {
		$this->m_sFilename = $sFilename;
		$this->m_sFilepath = $sFilepath;
	}
	
	public function getFilename() {
		return $this->m_sFilename;
	}
	
	public function getFileData() {
		return file_get_contents($this->m_sFilepath);
	}
	
	public function printFileData() {
		echo file_get_contents($this->m_sFilepath);		
	}
	
	public static function load($sFilename, array $aConfig = array()) {
		$sObject = get_called_class();
		$oCache = parent::getWatena()->getCache();
		$sFilepath = parent::getWatena()->getPath($sFilename);
		if($sFilepath === false) throw new Exception("Cachefile does not exist: $sFilename");
		
		$sIdentifier = md5($sFilename);
		$nCacheExp = $oCache->get("CACHEFILE_{$sIdentifier}_EXPIRATION", 0);
		$nFileExp = filemtime($sFilepath);
		
		$oObj = null;		
		if($nFileExp > $nCacheExp) {
			$aIncludes = array();
			try {
				list($oObject, $oRequirements) = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, array($aConfig), null, 'CacheableFile', null);
				$oCache->set("CACHEFILE_{$sIdentifier}_EXPIRATION", $mData);
				$oCache->set("CACHEFILE_{$sIdentifier}_REQUIREMENTS", $oRequirements);
				$oCache->set("CACHEFILE_{$sIdentifier}_OBJECT", $oObject);
			}
            catch(WatCeption $e) {
				throw new WatCeption('An exception occured while loading the required object.', array('object' => $sObject, 'file' => $sFilename), $this, $e);
            }
		}
		else {
			$oRequirements = $oCache->get("CACHEFILE_{$sIdentifier}_REQUIREMENTS", null);
			if($oRequirements->IsSucces()) {
				$oObject = $oCache->get("CACHEFILE_{$sIdentifier}_OBJECT", null);
				return $oObject;
			}
			else {
				throw new WatCeption('A previously loaded and cached object no longer meets it requirements.', array('object' => $sObject, 'requirements' => $oRequirements), $this);
			}
		}
	}
}

?>