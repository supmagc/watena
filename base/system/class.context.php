<?php

class Context extends Object {
	
	private $m_aPlugins = array();
	private $m_aDataFiles = array(); 
	private $m_aLibraryPaths = array();
	private $m_aFilterGroups = null;
	
	public function init() {
		$aProjects = explode(',', parent::getWatena()->getConfig('LIBRARIES', ''));
		foreach($aProjects as $sProject) {
			$sProject = Encoding::trim($sProject);
			$sPath = realpath(PATH_LIBS . "/$sProject");
			if($sPath === null) throw new WatCeption($sMessage);
			else $this->m_aLibraryPaths []= $sPath;
		}
		$this->loadPlugins(array_map('trim', explode(',', parent::getWatena()->getConfig('PLUGINS', ''))));		
	}
	
	/**
	 * Retrieve a list with all filtergroups found on the system.
	 * Since the groups are not loaded by default, this function handles the caching.
	 * 
	 * return array
	 */
	public function getFilterGroups() {
		if($this->m_aFilterGroups === null) {
			$this->m_aFilterGroups = array();
			foreach($this->m_aLibraryPaths as $sLibrary) {
				$sFiltersPath = realpath($sLibrary . '/filters/');
				if($sFiltersPath !== false) {
					$this->m_aFilterGroups []= FilterGroup::create($sFiltersPath);
				}
			}
			// Add the last default filtergroup
			$this->m_aFilterGroups []= FilterGroup::create(parent::getWatena()->getPath('b:filters'));
		}
		return $this->m_aFilterGroups;
	}
	
	/**
	 * Retrieve the path of the specified file on the system
	 * Their is an order of presedence:
	 * 1) Check base path
	 * 2) Check if file has a library prepending (lib$file)
	 * 3) If a preferred library is set, check it
	 * 4) Check all libraries on the system
	 * 
	 * @param string $sDirectory
	 * @param string $sFile
	 * @param string $sPreferredLibrary
	 * 
	 * @return string (or false)
	 */
	public function getLibraryFilePath($sDirectory, $sFile, $sPreferredLibrary = null) {
		$sSearch = "/$sDirectory/$sFile";
		if(($sTemp = realpath(PATH_BASE . $sSearch)) !== false) return $sTemp;
		if(($nIndex = Encoding::indexOf($sFile, '$')) !== false && ($sTemp = realpath(PATH_LIBS . '/' . Encoding::substring($sFile, 0, $nIndex) . "/$sDirectory/" . Encoding::substring($sFile, $nIndex + 1))) !== false) return $sTemp;
		if($sPreferredLibrary != null && ($sTemp = realpath(PATH_LIBS . "/$sPreferredLibrary" . $sSearch)) !== false) return $sTemp;
		foreach($this->m_aLibraryPaths as $sPath) {
			if(($sTemp = realpath($sPath . $sSearch)) !== false) return $sTemp;
		}
		return false;
	}
	
	/**
	 * Try to retrieve a plugin based on its name.
	 * It's possible to check if the class implements a specified interface.
	 * !!! WARNING !!! This does not automatically loads the plugin !!!
	 * 
	 * @param string $sPlugin
	 * @param string $sImplements (default = null)
	 * @return mixed The instance of the specified plugin, or null
	 */
	public function getPlugin($sPlugin, $sImplements = null) {
		$sKey = strtolower($sPlugin);
		$oPlugin = isset($this->m_aPlugins[$sKey]) ? $this->m_aPlugins[$sKey] : null;
		if($oPlugin) {
			if($sImplements && !in_array($sImplements, class_implements($oPlugin, false)))
				parent::terminate("The plugin you requested is loaded, but doesn implement the required interface: $sPlugin::$sImplements");
		}
		return $oPlugin;
	}
	
	/**
	 * Try to load a whole list of plugins if they're not loaded allready
	 * 
	 * @param string $sPlugins
	 * @param boolean $bTerminate (default = false)
	 * @return boolean Indicator if the plugins were loaded
	 */
	public function loadPlugins(array $aPlugins, $bTerminate = true) {
		$bSucces = true;
		foreach($aPlugins as $sPlugin) {
			$bSucces = $bSucces && $this->LoadPlugin($sPlugin, $bTerminate);
		}
		return $bSucces;
	}
	
	/**
	 * Try to load a plugin is it's not loaded allready
	 * 
	 * @param string $sPlugin
	 * @return boolean Indicator if the plugin was loaded
	 */
	public function loadPlugin($sPlugin) {
		$sKey = Encoding::toLower($sPlugin);
		$sFilePHP = $this->getLibraryFilePath('plugins', "plugin.$sKey.php");
		$sFileINI = $this->getLibraryFilePath('plugins', "config.$sKey.ini");
		if($sFilePHP === false) throw new WatCeption('Unable to find a library that contains the plugin.', array('plugin' => $sPlugin));
		if(!isset($this->m_aPlugins[$sKey])) {
			$aConfig = parent::getWatena()->getCache()->retrieve(
				"W_PLUGININI_$sPlugin", 
				create_function(
					'$a', 
					'return file_exists($a) ? parse_ini_file($a, true) : array();'),
				5, array($sFileINI));
			include_once $sFilePHP;
			$oPlugin = CacheableData::createObject($sPlugin, $aConfig, array(), null, $sFilePHP, 'Plugin');
			$this->m_aPlugins[$sKey] = $oPlugin;
		}
		return $this->m_aPlugins[$sKey] !== null;
	}
	
	/**
	 * Try to load a specified class and retrieve an instance of it
	 * 
	 * @param string $sClassName
	 * @param array $aConfig
	 * @param string $sIncludeFile
	 * @param string $sExtends
	 * @param string $sImplements
	 */
	public function loadObjectAndRequirements($sObjectName, array $aParams = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		// Include main file
		if($sIncludeFile) {
			if(file_exists($sIncludeFile)) include_once($sIncludeFile);
			else throw new WatCeption('The object to be loaded needs to include an unexisting file.', array('object' => $sObjectName, 'file' => $sIncludeFile), $this);
		}
		
		if(!class_exists($sObjectName, false)) throw new WatCeption('The class of the object to be loaded could not be found.', array('object' => $sObjectName), $this);		
		
		$aExtends = class_parents($sObjectName);
		$aImplements = class_implements($sObjectName);
		
		if(!in_array("Object", $aExtends)) throw new WatCeption('The object top be loaded does not extend \'Object\'.', array('object' => $sObjectName), $this);
		if($sExtends && !in_array($sExtends, $aExtends)) throw new WatCeption('The object to be loaded does not extend the required class.', array('object' => $sObjectName, 'class' => $sExtends), $this);
		if($sImplements && !in_array($sImplements, $aImplements)) throw new WatCeption('The object to be loaded does not implement the required interface.', array('object' => $sObjectName, 'interface' => $sImplements), $this);
		
		// Check requirements if possible/required
		$oRequirement = method_exists($sObjectName, 'getRequirements') ? new RequirementBuffer(call_user_func(array($sObjectName, 'getRequirements'))) : new RequirementBuffer();
		if($sIncludeFile) $oRequirement->addInclude($sIncludeFile);
		foreach($aExtends as $sParent) {
			if(method_exists($sParent, 'getRequirements')) {
				$oRequirement->addRequirements(call_user_func(array($sObjectName, 'getRequirements')));
			}
		}
		
		// Create instance
		if($oRequirement->isSucces()) {
			$oClass = new ReflectionClass($sObjectName);
			$oTmp = $oClass->newInstanceArgs($aParams);			
			return array($oTmp, $oRequirement);
		}
		else {
			throw new WatCeption('The object you are loading has some requirements that couldn\'t be met.', array('object' => $sObjectName, 'errors' => $oRequirement->getErrors(), 'requirements' => $oRequirement), $this);
		}
	}
	
	/**
	 * Get an array with respectibly the MVC components for the given mapping
	 * 
	 * @param Mapping $oMapping
	 * @return array(Model, View, Controller)
	 */
	public function getMVC(Mapping $oMapping) {
		$aFilterGroups = $this->getFilterGroups();
		$oModel = null;
		$oView = null;
		$oController = null;
		$oTheme = null;
		foreach($aFilterGroups as $oFilterGroup) {
			$aFilters = $oFilterGroup->getFilters();
			foreach($aFilters as $nOrder => $oFilter) {
				if($oFilter->match($oMapping)) {
					if(!$oModel) $oModel = $oFilter->createModel();
					if(!$oView) $oView = $oFilter->createView();
					if(!$oController) $oController = $oFilter->createController();
				}
			}
		}
		return array($oModel, $oView, $oController);
	}

	/**
	 * Retrieve a linked datafile-object
	 * 
	 * @param DataFile $sPath
	 */
	public function getDataFile($sPath) {
		if(!isset($this->m_aDataFiles[$sPath])) {
			$this->m_aDataFiles[$sPath] = new DataFile($sPath);
		}
		return $this->m_aDataFiles[$sPath];
	}
}

?>