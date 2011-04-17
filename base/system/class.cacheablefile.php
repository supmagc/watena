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
			$aIncludes = array();
			try {
				list($oObject, $oRequirements) = parent::getWatena()->getContext()->loadObjectAndRequirements($sObject, array($aConfig), $sIncludeFile, $sExtends, $sImplements);
				if($oRequirements->isSucces()) {
					$oCache->set("CACHEFILE_{$sIdentifier}_EXPIRATION", $mData);
					$oCache->set("CACHEFILE_{$sIdentifier}_REQUIREMENTS", $oRequirements);
					$oCache->set("CACHEFILE_{$sIdentifier}_OBJECT", $oObject);
				}
				else {
					throw new WatCeption('Unable to load')
				}
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
		}
	}
}

?>