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
		$sKey = Encoding::stringToLower($sPlugin);
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
	public function loadClass($sClassName, array $aConfig = array(), $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		// Include main file
		if($sIncludeFile) {
			if(file_exists($sIncludeFile)) include_once($sIncludeFile);
			else parent::terminate("Unable to include unexisting file $sIncludeFile.");
		}
		
		// Check inheritance stuff and existing
		if(!class_exists($sClassName, false)) parent::terminate("The class $sClassName could not be found.");
		if(!in_array("Cacheable", class_parents($sClassName))) parent::terminate("The class $sClassName does not extends Cacheable.");
		if($sExtends && !in_array($sExtends, class_parents($sClassName))) parent::terminate("The class $sClassName needs to extend $sExtends.");
		if($sImplements && !in_array($sImplements, class_implements($sClassName))) parent::terminate("The class $sClassName needs to implement $sImplements.");

		// Instantiate dependency holders
		$aIncludes = $sIncludeFile ? array($sIncludeFile) : array();
		$aExtensionLoads = array();
		$aPluginLoads = array();
		
		// CHeck requirements if possible/required
		if(method_exists($sClassName, 'getRequirements') && self::checkRequirements(call_user_func(array($sClassName, 'getRequirements')), true, $aIncludes, $aExtensionLoads, $aPluginLoads))
		
		// Create instance
		$oTmp = new $sClassName($aConfig);
		return array($aIncludes, $aExtensionLoads, $aPluginLoads, serialize($oTmp));
	}
	
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
		foreach($aFilters as $nOrder => $oFilter) {
			if($oFilter->match($oMapping)) {
				if(!$oModel) $oModel = $oFilter->getModel();
				if(!$oView) $oView = $oFilter->getView();
				if(!$oController) $oController = $oFilter->getController();
			}
		}
		return array($oModel, $oView, $oController);
	}
	
	/**
	 * Check the provided list with requirements and their compatibility.
	 * 'extension' => Required PHP-Extensions (This call uses 'dl' when available)
	 * 'plugins' => Required Watena-plugins
	 * 'pear' => Required pear installs 
	 * 'files' => A list of required files to include
	 * 
	 * @param array $aRequirements An array formatted to the requirement specifications.
	 * @param boolean $bTerminate Indicator if we should autoterminate whan the requirements are not meat. (default = true)
	 * @param array $aIncludes (out) Returns by reference a list with all the required includes
	 * @param array $aExtensionLoads (out) Returns by reference a list with all the required extensions
	 * @param array $aPluginLoads (out) Returns by reference a list with all the required plugins
	 */
	public function checkRequirements($aRequirements, $bTerminate = true, &$aIncludes = null, &$aExtensionLoads = null, &$aPluginLoads = null) {
		$bSucces = true;
		if($bSucces && $aRequirements && isset($aRequirements['extensions'])) {
			if(!is_array($aRequirements['extensions'])) $aRequirements['extensions'] = array($aRequirements['extensions']);
			foreach($aRequirements['extensions'] as $sExtension) {
				if(!extension_loaded($sExtension)) {
					$sFile = (PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '') . $sExtension . '.' . PHP_SHLIB_SUFFIX;
					if(!function_exists('dl') || !@dl($sFile)) {						
						if($bTerminate) parent::terminate("The required php-extension was not loaded: $sFile");
						$bSucces = false;
						break;
					}
					else if(is_array($aExtensionLoads)) {
						$aExtensionLoads []= $sFile;
					}
				}
			}
		}
		if($bSucces && $aRequirements && isset($aRequirements['plugins'])) {
			if(!is_array($aRequirements['plugins'])) $aRequirements['plugins'] = array($aRequirements['plugins']);
			foreach($aRequirements['plugins'] as $sPlugin) {
				if(!self::loadPlugin($sPlugin, false)) {
					if($bTerminate) parent::terminate("The required watena-plugin was not loaded: $sPlugin");
					$bSucces = false;
					break;
				}
				else if(is_array($aPluginLoads)) {
					$aPluginLoads []= $sPlugin;
				}
			}
		}
		if($bSucces && $aRequirements && isset($aRequirements['pear'])) {
			$nOld = error_reporting(E_ERROR);
			$bTemp = @include_once('PEAR.php');
			if($bTemp && class_exists('PEAR')) {
				if(!is_array($aRequirements['pear'])) $aRequirements['pear'] = array($aRequirements['pear']);
				foreach($aRequirements['pear'] as $sPear) {
					$bTemp = @include_once($sPear.'.php');		
					if(!$bTemp || !class_exists($sPear)) {
						if($bTerminate) parent::terminate("The required pear-install was not loaded: $sPear");
						$bSucces = false;
						break;
					}
					if(is_array($aIncludes)) $aIncludes[]= $sPear . '.php';
				}
			}
			else {
				if($bTerminate) parent::terminate("PEAR was not installed on this system.");
				$bSucces = false;
			}
			error_reporting($nOld);
		}
		if($bSucces && $aRequirements && isset($aRequirements['files'])) {
			foreach($aRequirements['files'] as $sFile) {
				if(!file_exists($filename)) {
					if($bTerminate) parent::terminate("The required could not be found: $sFile");
				}
				else if(is_array($aIncludes)) {
					$aIncludes[]= $sFile;
				}
			}
		}
		return $bSucces;						
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