<?php namespace Watena\Core;
/**
 * Manages the context wherein Watena is run.
 * As the Watena class is meant to provide access to the core mechanics 
 * of the system, The context provides context-aware access on demand.
 * This is the point of entry when you need to handle libraries, plugins, 
 * models, views, controllers a,d datafiles
 * 
 * @author Jelle
 * @version 0.1.0
 */
class Context extends Object {
	
	private $m_aPlugins = array();
	private $m_aDataFiles = array();
	private $m_aLibraries = array();
	private $m_aLibraryPaths = array();
	private $m_aFilterGroups = null;
	private $m_sPreferredLibrary = null;
	private $m_bRequirementWatchdog = false;

	/**
	 * Creates a new context instance.
	 * This should mainly be called when loading Watena.
	 */
	public function __construct() {
	}

	/**
	 * Load the given libraries.
	 * This makes all content inside any valid library available for loading.
	 * 
	 * You must call this method separably after loading and initializing the Watena object since
	 * It's possible that some libraries require Watena to be fully loaded on init.
	 * 
	 * @param array $aLibraries
	 */
	public final function loadLibraries(array $aLibraries) {
		foreach($aLibraries as $sLibraryName) {
			$sLibrary = trim($sLibraryName);
			$sPath = realpath(PATH_LIBS . "/$sLibrary");

			if(!$sPath) {
				$this->getLogger()->warning("One of the specified library-paths could not be mapped, and seems to not exist: {library}", array('library' => $sLibrary));
			}
			else {
				new ClassLoader("Watena\\Libs\\$sLibraryName", $sPath . "\src");
				array_push($this->m_aLibraries, $sLibrary);
				array_push($this->m_aLibraryPaths, $sPath);
			}
		}
	}

	/**
	 * Get a list of all the libraries.
	 * This will most of the time match the config value.
	 * 
	 * @return string[]
	 */
	public final function getLibraries() {
		return $this->m_aLibraries;
	}

	/**
	 * Retrieve an array with the full paths of the library folders.
	 * 
	 * @return string[]
	 */
	public final function getLibraryPaths() {
		return $this->m_aLibraryPaths;
	}
	
	/**
	 * Set the preferred library.
	 * The preferred library gets priority when searching for files, models, views, ...
	 * 
	 * @param string $sPreferredLibrary
	 */
	public final function setPreferredLibrary($sPreferredLibrary) {
		$this->m_sPreferredLibrary = $sPreferredLibrary;
	}
	
	/**
	 * Get the current preferred library.
	 * 
	 * @return string
	 */
	public final function getPreferredLibrary() {
		return $this->m_sPreferredLibrary;
	}
	
	/**
	 * Retrieve a list with all filter-groups found on the system.
	 * Since the groups are not loaded by default, this function handles the caching.
	 * 
	 * @return FilterGroup[]
	 */
	public final function getFilterGroups() {
		if($this->m_aFilterGroups === null) {
			foreach($this->m_aLibraryPaths as $sLibrary) {
				$sFiltersPath = realpath($sLibrary . '/filters/');
				if($sFiltersPath !== false) {
					$this->getLogger()->info("Context found the filters for library \'{library}\'.", array('library' => $sLibrary));
					$this->m_aFilterGroups []= FilterGroup::create($sFiltersPath);
				}
			}
		}
		return $this->m_aFilterGroups;
	}
	
	/**
	 * Retrieve the path of the specified file on the system
	 * Their is an order of precedence:
	 * 1) Check if path has a library indicator (lib@file)
	 * 2) If a local preferred library is set, check it
	 * 3) If a global preferred library is set, check it
	 * 4) Check all libraries on the system
	 * 
	 * @param string $sDirectory
	 * @param string $sFile
	 * @param boolean $bAllOfThem
	 * @param string $sPreferredLibrary
	 * @param mixed $o_mLibrary
	 * 
	 * @return string|false|array String when found, false when not found, array when $bAllOfThem is true.
	 */
	public final function getLibraryFilePath($sDirectory, $sFile, $bAllOfThem = false, $sPreferredLibrary = null, &$o_mLibrary = null) {
		$aReturnPaths = array();
		$aReturnLibraries = array();
		$aLibraries = array();
		
		// Start the ordered library list with prepended library
		if(($nIndex = Encoding::indexOf($sFile, '@')) !== false) {
			$sLibrary = Encoding::substring($sFile, 0, $nIndex);
			$sFile = Encoding::substring($sFile, $nIndex + 1);
			$aLibraries[$sLibrary] = null;
		}
		
		// Add the local preferred library to the ordered library list
		if(!empty($sPreferredLibrary)) {
			$aLibraries[$sPreferredLibrary] = null;
		}

		// Add the global preferred library to the ordered library list
		if(!empty($this->m_sPreferredLibrary)) {
			$aLibraries[$this->m_sPreferredLibrary] = null;
		}
		
		// Add the remaining libraries to the ordered library list
		foreach($this->m_aLibraries as $sLibrary) {
			$aLibraries[$sLibrary] = null;
		}

		// Verify file existing in the ordered library list
		$sSearch = !empty($sDirectory) ? "$sDirectory/$sFile" : $sFile;
		foreach($aLibraries as $sLibrary => $ign) {
			if(($sTemp = realpath(PATH_LIBS . "/$sLibrary/$sSearch")) !== false) {
				if($bAllOfThem) {
					$aReturnPaths []= $sTemp;
					$aReturnLibraries []= $sLibrary;
				}
				else {
					$o_mLibrary = $sLibrary;
					return $sTemp;
				}
			}
		}

		// Return the array when $bAllOfThem is true, or false when none found
		if($bAllOfThem) {
			$o_mLibrary = $aReturnLibraries;
			return $aReturnPaths;
		}
		else {
			return false;
		}
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
				$this->getLogger()->terminate("The plugin you requested is loaded, but doesn't implement the required interface: $sPlugin::$sImplements");
		}
		return $oPlugin;
	}
	
	/**
	 * Try to load a whole list of plugins if they're not loaded already
	 *
	 * @param string[] $aPlugins
	 * @param bool $bTerminate
	 * @return bool
	 */
	public final function loadPlugins(array $aPlugins, $bTerminate = true) {
		$bSuccess = true;
		foreach($aPlugins as $sPlugin) {
			if(strlen(trim($sPlugin)) > 0)
				$bSuccess = $bSuccess && $this->LoadPlugin($sPlugin, $bTerminate);
		}
		return $bSuccess;
	}
	
	/**
	 * Try to load a plugin is it's not loaded already
	 * 
	 * @param string $sPlugin
	 * @return bool Indicator if the plugin was loaded
	 */
	public final function loadPlugin($sPlugin) {
		$sKey = Encoding::toLower($sPlugin);
		$sFilePHP = $this->getLibraryFilePath('plugins', "plugin.$sKey.php");
		$aFileINIs = $this->getLibraryFilePath('plugins', "config.$sKey.ini", true);
		if($sFilePHP === false) $this->getLogger()->terminate('Unable to find a library that contains the required plugin: {plugin}', array('plugin' => $sPlugin));
		if(!isset($this->m_aPlugins[$sKey])) {
			$this->getLogger()->info('Loading plugin \'{plugin}\' from \'{php}\' with \'{ini}\'', array('plugin' => $sPlugin, 'php' => $sFilePHP, 'ini' => implode(', ', $aFileINIs)));
			require_once $sFilePHP;
			$aConfig = count($aFileINIs) > 0 ? IniParser::createFromFiles($aFileINIs)->getData(parent::getWatena()->getConfig()->configName()) : array();
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
		return Model::createClass($sName, $aParams, $aParams);
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
		return View::createClass($sName, $aParams, $aParams);
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
		return Controller::createClass($sName, $aParams, $aParams);
	}
	
	/**
	 * Try to load a specified class and retrieve an instance of it
	 * 
	 * @param string $sObjectName
	 * @param array $aParams
	 * @param string $sIncludeFile
	 * @param string $sExtends
	 * @param array $aImplements
	 * @return Object|false
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
			if($sImplements && !in_array($sImplements, $aImplementsFound)) $this->getLogger()->terminate('The object to be loaded does not implement the required interface.', array('object' => $sObjectName, 'interface' => $sImplements), $this);
		
		$this->m_bRequirementWatchdog = false;
		$oClass = new \ReflectionClass($sObjectName);
		$oTmp = $oClass->newInstanceArgs($aParams);
		return $oTmp;
	}
	
	/**
	 * Get an array with respectably the MVC components for the given mapping
	 * 
	 * @param Mapping $oMapping
	 * @return array(Model, View, Controller)
	 */
	public final function getMVC(Mapping $oMapping) {
		$aFilterGroups = $this->getFilterGroups();
		$oModel = null;
		$oView = null;
		$oController = null;
		foreach($aFilterGroups as $oFilterGroup) {
			$aFilters = $oFilterGroup->getFilters();
			foreach($aFilters as $nOrder => $oFilter) {
				if($oFilter->matches($oMapping)) {
					// Load model
					if(null != $oFilter->getModelData()) 
						$oModel = $this->loadModel($oFilter->getModelData()->getClass(), $oFilter->getModelData()->getParams());
						
					// Load view
					if(null != $oFilter->getViewData()) 
						$oView = $this->loadView($oFilter->getViewData()->getClass(), $oFilter->getViewData()->getParams());
					
					// Load controller
					if(null != $oFilter->getControllerData()) 
						$oController = $this->loadController($oFilter->getControllerData()->getClass(), $oFilter->getControllerData()->getParams());
					
					// Return data
					if($oModel || $oView || $oController)
						return array($oModel, $oView, $oController);						
				}
			}
		}
		return array(null, null, null);
	}

	/**
	 * Retrieve a linked datafile-object
	 * 
	 * @param string $sPath
	 * @return DataFile
	 */
	public final function getDataFile($sPath) {
		if(!isset($this->m_aDataFiles[$sPath])) {
			$this->m_aDataFiles[$sPath] = new DataFile($sPath);
		}
		return $this->m_aDataFiles[$sPath];
	}
}
