<?php

class Libraries extends CacheableData {
	
	//private $m_aProjectGroups = array();
	private $m_aPaths = array();
	private $m_aFilterGroups = array();
	
	public function init() {
		$aProjects = explode(',', parent::getWatena()->getConfig('LIBRARIES', ''));
		foreach($aProjects as $sProject) {
			$sProject = Encoding::trim($sProject);
			$sPath = realpath(PATH_LIBS . "/$sProject");
			if($sPath === null) throw new WatCeption($sMessage);
			else {
				$this->m_aPaths []= $sPath;
				$sFiltersPath = realpath($sPath . '/filters/');
				if($sFiltersPath !== false) $this->m_aFilterGroups []= FilterGroup::create($sFiltersPath);
			}
		}
		
		// Add the last default filtergroup
		$this->m_aFilterGroups []= FilterGroup::create(parent::getWatena()->getPath('b:filters'));
	}
	
	public function getProjectFilePath($sDirectory, $sFile, $sPreferredLibrary = null) {
		if(($sTemp = realpath(PATH_BASE . "/$sDirectory/$sFile")) !== false) return $sTemp;
		if(($nIndex = Encoding::indexOf($sFile, '$')) !== false && ($sTemp = realpath(PATH_LIBS . '/' . Encoding::substring($sFile, 0, $nIndex) . "/$sDirectory/" . Encoding::substring($sFile, $nIndex + 1))) !== false) return $sTemp;
		if($sPreferredLibrary != null && ($sTemp = realpath(PATH_LIBS . "/$sPreferredLibrary/$sDirectory/$sFile")) !== false) return $sTemp;
		foreach($this->m_aPaths as $sPath) {
			if((!$sTemp = realpath($sPath . "/$sDirectory")) !== false) return $sTemp;
		}
		return false;
	}
	
	public function getFilterGroups() {
		return $this->m_aFilterGroups;
	}
}
?>
