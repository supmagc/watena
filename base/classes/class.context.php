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
		if(!isset($this->m_aPlugins[$sKey])) {
			list($aIncludes, $oPlugin) = parent::getWatena()->getCache()->retrieve("W_PLUGIN_$sPlugin", array($this, '_loadPluginFromFile'), 5, array($sPlugin, $bTerminate));
			foreach($aIncludes as $sInclude) {
				require_once $sInclude;
			}
			$this->m_aPlugins[$sKey] = unserialize($oPlugin);
		}
		return $this->m_aPlugins[$sKey] !== null;
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
				if(isset($aFilters[$oFilter->getOrder()])) parent::terminate('A filter with this order-number allready exists: ' . $oFilter->getOrder() . '{' . $aFiles[$oFilter->getOrder()->getName()] . ', ' . $oFilter->getName() . '}');
				$aFilters[$oFilter->getOrder()] = $oFilter; 
			}
		}
		krsort($aFilters);
		return $aFilters;
	}

	/**
	 * Callback method that loads a plugin and its configuration from the filesystem.
	 */
	public function _loadPluginFromFile($sPlugin, $bTerminate = true) {
		$sKey = strtolower($sPlugin);
		$sFilePHP = PATH_BASE . "/plugins/plugin.$sKey.php";
		$sFileINI = PATH_BASE . "/plugins/config.$sKey.ini";
		if(file_exists($sFilePHP)) {
			require_once $sFilePHP;
			if(class_exists($sPlugin, false)) {
				if(in_array("Plugin", class_parents($sPlugin, false))) {
					
					$aSettings = parent::getWatena()->getCache()->retrieve(
						"W_PLUGININI_$sPlugin", 
						create_function(
							'$a', 
							'return file_exists($a) ? parse_ini_file($a, true) : array();'),
						5, array($sFileINI));
					
					$oPlugin = new $sPlugin($aSettings);
					$aIncludes = array($sFilePHP);
					if(self::_checkRequirements($oPlugin->getRequirements(), $bTerminate, $aIncludes)) {
						return array($aIncludes, serialize($oPlugin));
					}
				}
				else if($bTerminate) {
					$this->Terminate("Plugin class found, but doesn't inherit Plugin as parent object.");
				}
			}
			else if($bTerminate) {
				$this->Terminate("Plugin file found, but unable to find class: $sPlugin");
			}
		}
		else if($bTerminate) {
			$this->Terminate("Unable to find plugin-file: $sFilePHP");
		}
		return null;
	}
	
	/**
	 * Check the provided list with requirements and their compatibility.
	 * 
	 * @param array $aRequirements
	 * @param boolean $bTerminate
	 * @param array $aIncludes (out)
	 */
	private function _checkRequirements($aRequirements, $bTerminate = true, &$aIncludes = null) {
		$bSucces = true;
		if($bSucces && $aRequirements && isset($aRequirements['extensions'])) {
			if(!is_array($aRequirements['extensions'])) $aRequirements['extensions'] = array($aRequirements['extensions']);
			foreach($aRequirements['extensions'] as $sExtension) {
				if(!extension_loaded($sExtension)) {
					$sPrefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
					if(!function_exists('dl') || !@dl($prefix . $sExtension . '.' . PHP_SHLIB_SUFFIX)) {						
						if($bTerminate) parent::terminate("The required php-extension was not loaded: $sExtension");
						$bSucces = false;
						break;
					}
				}
			}
		}
		if($bSucces && $aRequirements && isset($aRequirements['plugins'])) {
			if(!is_array($aRequirements['plugins'])) $aRequirements['plugins'] = array($aRequirements['extensions']);
			foreach($aRequirements['plugins'] as $sPlugin) {
				if(self::loadPlugin($sPlugin, false)) {
					if($bTerminate) parent::terminate("The required watena-plugin was not loaded: $sPlugin");
					$bSucces = false;
					break;
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
					if($aIncludes) $aIncludes[]= $sPear . '.php';
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