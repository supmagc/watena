<?php

/*
class _ProjectGroup extends CacheableDirectory {

	private $m_bHasFilters, $m_bHasModels, $m_bHasViews, $m_bHasControllers, $m_bHasComponents, $m_bHasTheme, $m_bHasPlugins;
	private $m_aFilters = array();
	private $m_oTheme = null;
	
	public function init() {
		$this->m_bHasFilters = parent::hasDirectory('filters');
		$this->m_bHasModels = parent::hasDirectory('models');
		$this->m_bHasViews = parent::hasDirectory('views');
		$this->m_bHasControllers = parent::hasDirectory('controllers');
		$this->m_bHasComponents = parent::hasDirectory('components');
		$this->m_bHasTheme = parent::hasDirectory('theme');
		$this->m_bHasPlugins = parent::hasDirectory('plugins');
	}
	
	public function wakeup() {
		if($this->m_bHasTheme)
			$this->m_oTheme = Theme::create(parent::getDirectoryPath() . '/theme');
		if($this->m_bHasFilters)
			$this->_loadFiltersFromFile();
	}
	
	private function _loadFiltersFromFile() {
		$aFiles = scandir(parent::getDirectoryPath() . '/filters/');
		$this->m_aFilters = array();
		foreach($aFiles as $sFile) {
			if(Encoding::RegMatch('filter\\.[_a-z0-9_]*\\.xml', $sFile)) {
				$oFilter = Filter::create('b:/filters/'.$sFile);
				if(isset($this->m_aFilters[$oFilter->getOrder()])) parent::terminate('A filter with this order-number allready exists: ' . $oFilter->getOrder() . ' {' . $aFilters[$oFilter->getOrder()]->getName() . ', ' . $oFilter->getName() . '}');
				$this->m_aFilters[$oFilter->getOrder()] = $oFilter; 
			}
		}
		krsort($this->m_aFilters);
	}
}
*/

class Libraries extends CacheableData {
	
	//private $m_aProjectGroups = array();
	private $m_aPaths = array();
	
	public function init() {
		$aProjects = explode(',', parent::getWatena()->getConfig('LIBRARIES', ''));
		foreach($aProjects as $sProject) {
			$sProject = Encoding::trim($sProject);
			$sPath = realpath(PATH_LIBS . "/$sProject");
			if($sPath === null) throw new WatCeption($sMessage);
			else $this->m_aPaths []= $sPath;
		}
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
}
?>
