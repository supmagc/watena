<?php

class Context extends Object {
	
	private $m_aPlugins = array();
	private $m_aDataFiles = array(); 
	
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
	 * @param boolean $bTerminate
	 * @return boolean Indicator if the plugin was loaded
	 */
	public function loadPlugin($sPlugin, $bTerminate = true) {
		$sKey = Encoding::toLower($sPlugin);
		$sFilePHP = PATH_BASE . "/plugins/plugin.$sKey.php";
		$sFileINI = PATH_BASE . "/plugins/config.$sKey.ini";
		if(!isset($this->m_aPlugins[$sKey])) {
			$aConfig = parent::getWatena()->getCache()->retrieve(
				"W_PLUGININI_$sPlugin", 
				create_function(
					'$a', 
					'return file_exists($a) ? parse_ini_file($a, true) : array();'),
				5, array($sFileINI));
			$oPlugin = Cacheable::create($sPlugin, $aConfig, 'W_PLUGIN_'.$sPlugin, 5, $sFilePHP, 'Plugin');
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
		
		if(!in_array("Object", $aParents)) throw new WatCeption('The object top be loaded does not extend \'Object\'.', array('object' => $sObjectName), $this);
		if($sExtends && !in_array($sExtends, $aParents)) throw new WatCeption('The object to be loaded does not extend the required class.', array('object' => $sObjectName, 'class' => $sExtends), $this);
		if($sImplements && !in_array($sImplements, $aImplements)) throw new WatCeption('The object to be loaded does not implement the required interface.', array('object' => $sObjectName, 'interface' => $sImplements), $this);
		
		// Check requirements if possible/required
		$oRequirement = method_exists($sClassName, 'getRequirements') ? new RequirementBuffer(call_user_func(array($sClassName, 'getRequirements'))) : new RequirementBuffer();
		if($sIncludeFile && is_array($aIncludes)) $aIncludes []= $sIncludeFile;
		foreach($aParents as $sParent) {
			if(method_exists($sParent, 'getRequirements')) {
				$oRequirement->addRequirements(call_user_func(array($sClassName, 'getRequirements')));
			}
		}
		
		// Create instance
		if($oRequirement->isSucces()) {
			$oTmp = new $sObjectName($aParams);
			return array($oTmp, $oRequirement);
		}
		else {
			throw new WatCeption('The object you are loading has some requirements that couldn\'t be met.', array('object' => $sObjectName, 'errors' => $oRequirement->getErrors(), 'requirements' => $oRequirement), $this);
		}
	}
	
	/*
	public function loadClass($sClassName, array $aConfig = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		// Include main file
		if($sIncludeFile) {
			if(file_exists($sIncludeFile)) include_once($sIncludeFile);
			else parent::terminate("Unable to include unexisting file $sIncludeFile.");
		}
		
		// Check inheritance stuff and existing
		$aParents = class_parents($sClassName, false);
		if(!class_exists($sClassName, false)) parent::terminate("The class $sClassName could not be found.");
		if(!in_array("Object", $aParents)) parent::terminate("The class $sClassName does not extends Object.");
		if($sExtends && !in_array($sExtends, $aParents)) parent::terminate("The class $sClassName needs to extend $sExtends.");
		if($sImplements && !in_array($sImplements, class_implements($sClassName))) parent::terminate("The class $sClassName needs to implement $sImplements.");

		// Instantiate dependency holders
		$aIncludes = $sIncludeFile ? array($sIncludeFile) : array();
		$aExtensionLoads = array();
		$aPluginLoads = array();
		
		// Check requirements if possible/required
		$bCanLoad = method_exists($sClassName, 'getRequirements') ? self::checkRequirements(call_user_func(array($sClassName, 'getRequirements')), true, $aIncludes, $aExtensionLoads, $aPluginLoads) : true;
		foreach($aParents as $sParent) {
			if(method_exists($sParent, 'getRequirements')) {
				$bCanLoad = $bCanLoad && self::checkRequirements(call_user_func(array($sClassName, 'getRequirements')), true, $aIncludes, $aExtensionLoads, $aPluginLoads);
			}
		}
		
		// Create instance
		$oTmp = new $sClassName($aConfig);
		return array($aIncludes, $aExtensionLoads, $aPluginLoads, serialize($oTmp));
	}
	*/
	
	/**
	 * Get an array with respectibly the MVC components for the given mapping
	 * 
	 * @param Mapping $oMapping
	 * @return array(Model, View, Controller)
	 */
	public function getMVC(Mapping $oMapping) {
		$aFilters = $this->getWatena()->getCache()->retrieve('W_FILTERS', array($this, '_loadFiltersFromFile'), 5);
		$oModel = null;
		$oView = null;
		$oController = null;
		$oTheme = null;
		foreach($aFilters as $nOrder => $oFilter) {
			if($oFilter->match($oMapping)) {
				if(!$oModel) $oModel = $oFilter->getModel();
				if(!$oView) $oView = $oFilter->getView();
				if(!$oController) $oController = $oFilter->getController();
			}
		}
		return array($oModel, $oView, $oController);
	}

	public function getDataFile($sPath) {
		if(!isset($this->m_aDataFiles[$sPath])) {
			$this->m_aDataFiles[$sPath] = new DataFile($sPath);
		}
		return $this->m_aDataFiles[$sPath];
	}
	
	/**
	 * Callback method that loads all filters from the filesystem.
	 */
	public function _loadFiltersFromFile() {
		$aFiles = scandir(PATH_BASE . '/filters/');
		$aFilters = array();
		foreach($aFiles as $sFile) {
			if(Encoding::RegMatch('filter\\.[_a-z0-9_]*\\.xml', $sFile)) {
				$sFile = parent::getWatena()->getPath('b:/filters/'.$sFile);
				$oFilter = Cacheable::create('Filter', array('file' => $sFile), "W_FILTER_$sFile", Cacheable::EXP_NEVER, null, null, null, filemtime($sFile));
				if(isset($aFilters[$oFilter->getOrder()])) parent::terminate('A filter with this order-number allready exists: ' . $oFilter->getOrder() . ' {' . $aFilters[$oFilter->getOrder()]->getName() . ', ' . $oFilter->getName() . '}');
				$aFilters[$oFilter->getOrder()] = $oFilter; 
			}
		}
		krsort($aFilters);
		return $aFilters;
	}
}

?>