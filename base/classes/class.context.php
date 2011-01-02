<?php

class Context extends Object {
	
	private $m_aPlugins = array();
	
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
			/*list($aIncludes, $oPlugin) = parent::getWatena()->getCache()->retrieve("W_PLUGIN_$sPlugin", array($this, '_loadPluginFromFile'), 5, array($sPlugin, $bTerminate));
			foreach($aIncludes as $sInclude) {
				require_once $sInclude;
			}*/
			$this->m_aPlugins[$sKey] = $oPlugin;
		}
		return $this->m_aPlugins[$sKey] !== null;
	}

	/**
	 * Retrieve a model based on the given mapping-object.
	 * 
	 * @param Mapping $oMapping
	 * @return Model
	 */
	public function matchFilterToModel(Mapping $oMapping) {
		$aFilters = $this->getWatena()->getCache()->retrieve('W_filters', array($this, '_loadFiltersFromFile'), 5);
		foreach($aFilters as $nOrder => $oFilter) {
			if($oFilter->match($oMapping)) {
				return $oFilter->getController();
			}
		}
		return null;
	}
	
	/**
	 * Retrieve a view based on the given mapping-object.
	 * 
	 * @param Mapping $oMapping
	 * @return View
	 */
	public function matchFilterToView(Mapping $oMapping) {
		$aFilters = $this->getWatena()->getCache()->retrieve('W_filters', array($this, '_loadFiltersFromFile'), 5);
		foreach($aFilters as $nOrder => $oFilter) {
			if($oFilter->match($oMapping)) {
				return $oFilter->getController();
			}
		}
		return null;
	}
	
	/**
	 * Retrieve a controller based on the given mapping-object.
	 * 
	 * @param Mapping $oMapping
	 * @return Controller
	 */
	public function matchFilterToController(Mapping $oMapping) {
		$aFilters = $this->getWatena()->getCache()->retrieve('W_filters', array($this, '_loadFiltersFromFile'), 5);
		foreach($aFilters as $nOrder => $oFilter) {
			if($oFilter->match($oMapping)) {
				return $oFilter->getController();
			}
		}
		return null;
	}

	/**
	 * Callback method that loads all filters from the filesystem.
	 */
	public function _loadFiltersFromFile() {
		$aFiles = scandir(PATH_BASE . '/filters/');
		$aFilters = array();
		foreach($aFiles as $sFile) {
			if(Encoding::RegMatch('filter\\.[_a-z0-9_]*\\.xml', $sFile)) {
				$oFilter = new Filter(file_get_contents(PATH_BASE . '/filters/' . $sFile));
				if(isset($aFilters[$oFilter->getOrder()])) parent::terminate('A filter with this order-number allready exists: ' . $oFilter->getOrder() . ' {' . $aFilters[$oFilter->getOrder()]->getName() . ', ' . $oFilter->getName() . '}');
				$aFilters[$oFilter->getOrder()] = $oFilter; 
			}
		}
		krsort($aFilters);
		return $aFilters;
	}
	
	/**
	 * Check the provided list with requirements and their compatibility.
	 * 
	 * @param array $aRequirements An array formatted to the requirement specifications.
	 * @param boolean $bTerminate Indicator if we should autoterminate whan the requirements are not meat. (default = true)
	 * @param array $aIncludes (out) Returns by reference a list with all the required includes
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
			if(!is_array($aRequirements['plugins'])) $aRequirements['plugins'] = array($aRequirements['extensions']);
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
		return $bSucces;						
	}
}

?>