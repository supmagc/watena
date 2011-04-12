<?php

class RequirementBuffer extends Object {
	
	private $m_aIncludes = array();
	private $m_aExtensions = array();
	private $m_aPlugins = array();
	private $m_aProblems = array();
	private $m_bSucces = true;
	
	public final function __construct(array $aRequirements = array()) {
		$this->addRequirements($aRequirements);
	}
	
	public final function __wakeup() {
		$this->load();
	}
	
	/**
	 * Check the provided list with requirements and their compatibility.
	 * 'extension' => Required PHP-Extensions (This call uses 'dl' when available)
	 * 'plugins' => Required Watena-plugins
	 * 'pear' => Required pear installs 
	 * 'files' => A list of required files to include
	 * 
	 * @param array $aRequirements An array formatted to the requirement specifications.
	 * @return bool Indicate if this set of requirements was succesfully loaded
	 */
	public final function addRequirements(array $aRequirements) {
		$bSucces = true;
		
		// Check extensions
		if($aRequirements && isset($aRequirements['extensions'])) {
			if(!is_array($aRequirements['extensions'])) $aRequirements['extensions'] = array($aRequirements['extensions']);
			foreach($aRequirements['extensions'] as $sExtension) {
				if(!extension_loaded($sExtension)) {
					$sFile = (PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '') . $sExtension . '.' . PHP_SHLIB_SUFFIX;
					if(function_exists('dl') && @dl($sFile)) {						
						$this->m_aExtensions []= $sFile;
					}
					else {
						$this->m_aProblems []= "The required php-extension was not loaded: $sFile";
						$bSucces = false;
					}
				}
			}
		}
		
		// Check plugins
		if($aRequirements && isset($aRequirements['plugins'])) {
			if(!is_array($aRequirements['plugins'])) $aRequirements['plugins'] = array($aRequirements['plugins']);
			foreach($aRequirements['plugins'] as $sPlugin) {
				if(self::loadPlugin($sPlugin, false)) {
					$this->m_aPlugins []= $sPlugin;
				}
				else {
					$this->m_aProblems []= "The required watena-plugin was not loaded: $sPlugin";
					$bSucces = false;
				}
			}
		}
		
		// Check PEAR
		if($aRequirements && isset($aRequirements['pear'])) {
			$nOld = error_reporting(E_ERROR);
			$bTemp = @include_once('PEAR.php');
			if($bTemp && class_exists('PEAR')) {
				$this->m_aIncludes []= 'PEAR.php';
				if(!is_array($aRequirements['pear'])) $aRequirements['pear'] = array($aRequirements['pear']);
				foreach($aRequirements['pear'] as $sPear) {
					$bTemp = @include_once($sPear.'.php');		
					if($bTemp && class_exists($sPear)) {
						$this->m_aIncludes []= $sPear . '.php';
					}
					else {
						$this->m_aProblems []= "The required pear-install was not loaded: $sPear";
						$bSucces = false;
					}
				}
			}
			else {
				$this->m_aProblems []= "PEAR was not installed on this system.";
				$bSucces = false;
			}
			error_reporting($nOld);
		}
		
		// Check files
		if($aRequirements && isset($aRequirements['files'])) {
			foreach($aRequirements['files'] as $sFile) {
				if(file_exists($filename)) {
					$this->m_aProblems []= "The required file could not be found: $sFile";
					$bSucces = false;
				}
				else if(is_array($aIncludes)) {
					$aIncludes[]= $sFile;
				}
			}
		}
		
		$this->m_bSucces = $this->m_bSucces && $bSucces;
		return $bSucces;					
	}
	
	/**
	 * Explicitly call to load all required files and extensions and plugins, ...
	 */
	public final function load() {
		foreach($this->m_aIncludes as $sFile) include_once $sFile;
		foreach($this->m_aExtensions as $sName) if(!extension_loaded($sName)) dl($sName);
		foreach($this->m_aPlugins as $sName) parent::getWatena()->getContext()->loadPlugin($sName);
	}
	
	/**
	 * Check if all requirements are met
	 * 
	 * @return bool
	 */
	public final function isSucces() {
		return $this->m_bSucces;
	}

	/**
	 * Scan the class structure as provided (or instance structure if needed)
	 * and return the RequirementBuffer for the specified object.
	 * 
	 * @param mixed $sClassName
	 * @return RequirementBuffer
	 */
	public static final function scanClass($sClassName) {
		$oRB = new RequirementBuffer();
		$aParents = class_parents($sClassName, false);
		if(method_exists($sClassName, 'getRequirements')) $oRB->addRequirements(call_user_func(array($sClassName, 'getRequirements')));
		foreach($aParents as $sParent) if(method_exists($sParent, 'getRequirements')) $oRB->addRequirements(call_user_func(array($sClassName, 'getRequirements')));
		return $oRB;
	}
}

?>