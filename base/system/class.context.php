<?php

class Context extends Object {
	
	private $m_aPlugins = array();
	private $m_aDataFiles = array(); 
	private $m_aLibraryPaths = array();
	private $m_aFilterGroups = null;
	private $m_bRequirementWatchdog = false;
	
	public function __construct() {
		$aProjects = parent::getWatena()->getConfig()->getLibraries();
		foreach($aProjects as $sProject) {
			$sProject = trim($sProject);
			$sPath = realpath(PATH_LIBS . "/$sProject");
			if($sPath === null) $this->getLogger()->warning("One of the specified library-paths could not be mapped, and seems to not exist: {library}", array('library' => $sProject));
			else $this->m_aLibraryPaths []= $sPath;
		}
	}

	/**
	 * Retrieve an array with the full paths of the library folders.
	 * 
	 * @return array
	 */
	public final function getLibraryPaths() {
		return $this->m_aLibraryPaths;
	}
	
	/**
	 * Retrieve a list with all filtergroups found on the system.
	 * Since the groups are not loaded by default, this function handles the caching.
	 * 
	 * return array
	 */
	public final function getFilterGroups() {
		if($this->m_aFilterGroups === null) {
			$this->m_aFilterGroups = array();
			foreach($this->m_aLibraryPaths as $sLibrary) {
				$sFiltersPath = realpath($sLibrary . '/filters/');
				if($sFiltersPath !== false) {
					$this->getLogger()->info("Context found the filters for library \'{library}\'.", array('library' => $sLibrary));
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
	public final function getLibraryFilePath($sDirectory, $sFile, $bAllOfThem = false, $sPreferredLibrary = null) {
		$aReturn = array();
		$sSearch = "/$sDirectory/$sFile";

		// Check path in base directory
		if(($sTemp = realpath(PATH_BASE . $sSearch)) !== false) 
			if($bAllOfThem) $aReturn[$sTemp] = null; else return $sTemp;

		// Check path with predefined library
		if(($nIndex = strpos($sFile, '$')) !== false && ($sTemp = realpath(PATH_LIBS . '/' . substr($sFile, 0, $nIndex) . "/$sDirectory/" . substr($sFile, $nIndex + 1))) !== false) 
			if($bAllOfThem) $aReturn[$sTemp] = null; else return $sTemp;
		
		// Check preferred library
		if($sPreferredLibrary != null && ($sTemp = realpath(PATH_LIBS . "/$sPreferredLibrary" . $sSearch)) !== false) 
			if($bAllOfThem) $aReturn[$sTemp] = null; else return $sTemp;
		
		// Check existing library directories
		foreach($this->m_aLibraryPaths as $sPath) {
			if(($sTemp = realpath($sPath . $sSearch)) !== false) 
				if($bAllOfThem) $aReturn[$sTemp] = null; else return $sTemp;
		}
		
		return $bAllOfThem ? array_keys($aReturn) : false;
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
	public final function getPlugin($sPlugin, $sImplements = null) {
		$sKey = strtolower($sPlugin);
		$oPlugin = (isset($this->m_aPlugins[$sKey]) || $this->loadPlugin($sPlugin)) ? $this->m_aPlugins[$sKey] : null;
		if($oPlugin) {
			if($sImplements && !in_array($sImplements, class_implements($oPlugin, false)))
				$this->getLogger()->terminate("The plugin you requested is loaded, but doesn implement the required interface: $sPlugin::$sImplements");
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
	public final function loadPlugins(array $aPlugins, $bTerminate = true) {
		$bSucces = true;
		foreach($aPlugins as $sPlugin) {
			if(strlen(trim($sPlugin)) > 0)
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
	public final function loadPlugin($sPlugin) {
		$sKey = Encoding::toLower($sPlugin);
		$sFilePHP = $this->getLibraryFilePath('plugins', "plugin.$sKey.php");
		$aFileINIs = $this->getLibraryFilePath('plugins', "config.$sKey.ini", true);
		if($sFilePHP === false) $this->getLogger()->terminate('Unable to find a library that contains the required plugin: {plugin}', array('plugin' => $sPlugin));
		if(!isset($this->m_aPlugins[$sKey])) {
			$this->getLogger()->debug('Loading plugin \'{plugin}\' from \'{php}\' with \'{ini}\'', array('plugin' => $sPlugin, 'php' => $sFilePHP, 'ini' => implode(', ', $aFileINIs)));
			require_once $sFilePHP;
			$aConfig = count($aFileINIs) > 0 ? IniParser::createFromFiles($aFileINIs)->getData(parent::getWatena()->getConfig()->getConfigName()) : array();
			$oPhpLoader = new CacheLoader($sPlugin);
			$oPhpLoader->addPathDependencies($aFileINIs);
			$oPhpLoader->addPathDependency($sFilePHP);
			$oPlugin = $oPhpLoader->get($aConfig);
			$this->m_aPlugins[$sKey] = $oPlugin;
		}
		return $this->m_aPlugins[$sKey] !== null;
	}

	/**
	 * Load and retrieve the specified model.
	 * 
	 * @param string $sName
	 * @param array $aParams
	 * 
	 * @return Model
	 */
	public final function loadModel($sName, array $aParams = array()) {
		$sFileName = 'model.' . Encoding::toLower($sName) . '.php';
		$sFilePath = parent::getWatena()->getContext()->getLibraryFilePath('models', $sFileName);
		return Model::includeAndCreate($sFilePath, $sName, $aParams, $aParams);
	}
	
	/**
	 * Load and retrieve the specified view.
	 * 
	 * @param string $sName
	 * @param array $aParams
	 * 
	 * @return View
	 */
	public final function loadView($sName, array $aParams = array()) {
		$sFileName = 'view.' . Encoding::toLower($sName) . '.php';
		$sFilePath = parent::getWatena()->getContext()->getLibraryFilePath('views', $sFileName);
		return View::includeAndCreate($sFilePath, $sName, $aParams, $aParams);
	}
	
	/**
	 * Load and retrieve the specified controller.
	 * 
	 * @param string $sName
	 * @param array $aParams
	 * 
	 * @return Controller
	 */
	public final function loadController($sName, array $aParams = array()) {
		$sFileName = 'controller.' . Encoding::toLower($sName) . '.php';
		$sFilePath = parent::getWatena()->getContext()->getLibraryFilePath('controllers', $sFileName);
		return Controller::includeAndCreate($sFilePath, $sName, $aParams, $aParams);
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
	public final function loadObjectAndRequirements($sObjectName, array $aParams = array(), $sIncludeFile = null, $sExtends = null, array $aImplements = array()) {
		$this->m_bRequirementWatchdog = true;
		
		// Include main file
		if($sIncludeFile) {
			require_includeonce($sIncludeFile);
		}
		
		if(!class_exists($sObjectName, false)) $this->getLogger()->terminate('The class of the object to be loaded could not be found.', array('object' => $sObjectName), $this);		
		
		$aExtendsFound = class_parents($sObjectName);
		$aImplementsFound = class_implements($sObjectName);
		
		if(!in_array("Object", $aExtendsFound)) $this->getLogger()->terminate('The object top be loaded does not extend \'Object\'.', array('object' => $sObjectName), $this);
		if($sExtends && !in_array($sExtends, $aExtendsFound)) $this->getLogger()->terminate('The object to be loaded does not extend the required class.', array('object' => $sObjectName, 'class' => $sExtends), $this);
		foreach($aImplements as $sImplements)
			if($sImplements && !in_array($sImplements, $aImplements)) $this->getLogger()->terminate('The object to be loaded does not implement the required interface.', array('object' => $sObjectName, 'interface' => $sImplements), $this);
		
		$this->m_bRequirementWatchdog = false;
		if(true) {
			$oClass = new ReflectionClass($sObjectName);
			$oTmp = $oClass->newInstanceArgs($aParams);			
			return $oTmp;
		}
		else {
			$this->getLogger()->terminate('The object you are loading has some requirements that couldn\'t be met.', array('object' => $sObjectName, 'errors' => $oRequirement->getErrors(), 'requirements' => $oRequirement), $this);
		}
	}
	
	/**
	 * Get an array with respectibly the MVC components for the given mapping
	 * 
	 * @param Mapping $oMapping
	 * @return array(Model, View, Controller)
	 */
	public final function getMVC(Mapping $oMapping) {
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
					break;
				}
			}
			if($oModel || $oView || $oController) break;
		}
		return array($oModel, $oView, $oController);
	}

	/**
	 * Retrieve a linked datafile-object
	 * 
	 * @param DataFile $sPath
	 */
	public final function getDataFile($sPath) {
		if(!isset($this->m_aDataFiles[$sPath])) {
			$this->m_aDataFiles[$sPath] = new DataFile($sPath);
		}
		return $this->m_aDataFiles[$sPath];
	}
}

?>