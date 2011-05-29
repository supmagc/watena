<?php

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

class Projects extends Object {
	
	private $m_aProjectGroups = array();
	
	public function __construct() {
		$aProjects = explode(',', parent::getWatena()->getConfig('PROJECTS', ''));
		foreach($aProjects as $sProject) {
			$sProject = Encoding::trim($sProject);
			$sPath = parent::getWatena()->getPath($sProject);
			$oGroup = _ProjectGroup::create($sPath);
			$this->m_aProjectGroups []= $oGroup;
		}
	}
	
	public function getProjectFilePath($sDirectory, $sFile, $sPreferedProject = null) {
		if(($sTemp = realpath(PATH_BASE . "/$sPreferedProject/$sDirectory/$sFile")) !== false) return $sTemp;
		if($sPreferedProject != null && ($sTemp = realpath(PATH_LIBS . "/$sDirectory/$sFile")) !== false) return $sTemp;
		foreach($this->m_aProjectGroups as $oGroup) {
			if((!$sTemp = realpath(PATH_LIBS . "/$sDirectory")) !== false) return $sTemp;
		}
	}
}
?>
