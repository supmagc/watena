<?php

class RequirementBuffer extends Object {
	
	private $m_aIncludes = array();
	private $m_aExtensions = array();
	private $m_aPlugins = array();
	private $m_aProblems = array();
	private $m_aDefines = array();
	private $m_aLibraries = array();
	private $m_bSucces = true;
	
	public final function __construct($aRequirements = null) {
		$this->addRequirements($aRequirements);
	}
	
	/**
	 * Called when the object is beeing deserialized.
	 * This will automatically call load()
	 * 
	 * You only need to check if isSucces() returns true
	 */
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
	public final function addRequirements($aRequirements) {
		$bSucces = true;
		
		if(is_array($aRequirements)) {

			if($aRequirements && isset($aRequirements['extensions'])) {
				if(is_array($aRequirements['extensions'])) $bSucces = $bSucces && $this->addExtensions($aRequirements['extensions']);
				else $bSucces = $bSucces && $this->addExtension($aRequirements['extensions']);
			}
			if($aRequirements && isset($aRequirements['plugins'])) {
				if(is_array($aRequirements['plugins'])) $bSucces = $bSucces && $this->addPlugins($aRequirements['plugins']);
				else $bSucces = $bSucces && $this->addPlugin($aRequirements['plugins']);
			}
			if($aRequirements && isset($aRequirements['pear'])) {
				if(is_array($aRequirements['includes'])) $bSucces = $bSucces && $this->addPears($aRequirements['includes']);
				else $bSucces = $bSucces && $this->addPear($aRequirements['includes']);
			}
			if($aRequirements && isset($aRequirements['includes'])) {
				if(is_array($aRequirements['extensions'])) $bSucces = $bSucces && $this->addIncludes($aRequirements['extensions']);
				else $bSucces = $bSucces && $this->addInclude($aRequirements['extensions']);
			}
			if($aRequirements && isset($aRequirements['defines'])) {
				if(is_array($aRequirements['defines'])) $bSucces = $bSucces && $this->addDefines($aRequirements['defines']);
				else $bSucces = $bSucces && $this->addDefine($aRequirements['defines']);
			}
			if($aRequirements && isset($aRequirements['libraries'])) {
				if(is_array($aRequirements['libraries'])) $bSucces = $bSucces && $this->addLibraries($aRequirements['libraries']);
				else $bSucces = $bSucces && $this->addLibrary($aRequirements['libraries']);
			}
			/*
			if($aRequirements && isset($aRequirements['models'])) {
				if(is_array($aRequirements['models'])) $bSucces = $bSucces && $this->addLibraries($aRequirements['models']);
				else $bSucces = $bSucces && $this->addLibrary($aRequirements['models']);
			}
			if($aRequirements && isset($aRequirements['views'])) {
				if(is_array($aRequirements['views'])) $bSucces = $bSucces && $this->addLibraries($aRequirements['views']);
				else $bSucces = $bSucces && $this->addLibrary($aRequirements['views']);
			}
			if($aRequirements && isset($aRequirements['controllers'])) {
				if(is_array($aRequirements['controllers'])) $bSucces = $bSucces && $this->addLibraries($aRequirements['controllers']);
				else $bSucces = $bSucces && $this->addLibrary($aRequirements['controllers']);
			}
			*/
		}

		$this->m_bSucces = $this->m_bSucces && $bSucces;
		return $bSucces;					
	}
	
	public final function addExtensions(array $aExtensions) {
		$bResult = true;
		foreach($aExtensions as $sExtension) $bResult = $bResult && $this->addExtension($sExtension);
		$this->m_bSucces = $this->m_bSucces && $bResult;
		return $bResult;
	}
	
	public final function addExtension($sExtension) {
		$sFile = (PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '') . $sExtension . '.' . PHP_SHLIB_SUFFIX;
		if(extension_loaded($sExtension) || (function_exists('dl') && @dl($sFile))) {
			$this->m_aExtensions []= $sExtension;
			return true;
		}
		else {
			$this->m_aProblems []= "The required php-extension could not be found: $sExtension";
			return false;
		}		
	}
	
	public final function addPlugins(array $aPlugins) {
		$bResult = true;
		foreach($aPlugins as $sPlugin) $bResult = $bResult && $this->addPlugin($sPlugin);
		$this->m_bSucces = $this->m_bSucces && $bResult;
		return $sPlugin;
	}
	
	public final function addPlugin($sPlugin) {
		if(parent::getWatena()->getContext()->loadPlugin($sPlugin)) {
			$this->m_aPlugins []= $sPlugin;
			return true;
		}
		else {
			$this->m_aProblems []= "The required watena-plugin could not be found: $sPlugin";
			return false;
		}		
	}
	
	public final function addPears(array $aPears) {
		$bResult = true;
		foreach($aPears as $sPear) $bResult = $bResult && $this->addPear($sPear);
		$this->m_bSucces = $this->m_bSucces && $bResult;
		return $sPear;
	}
	
	public final function addPear($sPear) {
		$nOld = error_reporting(E_ERROR);
		$bTemp0 = @include_once('PEAR.php');
		$bTemp1 = @include_once($sPear.'.php');
		$bResult = true;
		if($bTemp0 && $bTemp1) {
			$this->m_aIncludes []= 'PEAR.php';
			$this->m_aIncludes []= $sPear.'.php';
		}
		else {
			$this->m_aProblems []= "The required pear-module could not be found: $sPear";
			$bResult = false;
		}
		error_reporting($nOld);
		return $bResult;
	}
	
	public final function addIncludes(array $aIncludes) {
		$bResult = true;
		foreach($aIncludes as $sInclude) $bResult = $bResult && $this->addInclude($sInclude);
		$this->m_bSucces = $this->m_bSucces && $bResult;
		return $sInclude;
	}
	
	public final function addInclude($sInclude) {
		if(file_exists($sInclude)) {
			$this->m_aIncludes []= $sInclude;
			return true;
		}
		else {
			$this->m_aProblems []= "The required include-file could not be found: $sInclude";
			return false;
		}		
	}
	
	public final function addDefines(array $aDefines) {
		$bResult = true;
		foreach($aDefines as $sDefine) $bResult = $bResult && $this->addDefine($sDefine);
		$this->m_bSucces = $this->m_bSucces && $bResult;
		return $sDefine;
	}
	
	public final function addDefine($sDefine) {
		if(defined($sDefine)) {
			$this->m_aDefines []= $sDefine;
			return true;
		}
		else {
			$this->m_aProblems []= "The required define could not be found: $sDefine";
			return false;
		}		
	}
	
	public final function addLibraries(array $aLibraries) {
		$bResult = true;
		foreach($aLibraries as $sLibrary) $bResult = $bResult && $this->addLibrary($sLibrary);
		$this->m_bSucces = $this->m_bSucces && $bResult;
		return $bResult;
	}
	
	public final function addLibrary($sLibrary) {
		// TODO: implement library requirement check
	} 
	
	/**
	 * Explicitly call to load all required files and extensions and plugins, ...
	 */
	public final function load() {
		foreach($this->m_aIncludes as $sFile) include_once $sFile;
		foreach($this->m_aExtensions as $sName) if(!extension_loaded($sName)) dl($sName);
		foreach($this->m_aPlugins as $sName) parent::getWatena()->getContext()->loadPlugin($sName);
		foreach($this->m_aDefines as $sDefine) $this->m_bSucces = $this->m_bSucces && defined($sDefine);
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
	 * Retrieve all occured errors as an array
	 * 
	 * @return array
	 */
	public final function getErrors() {
		return $this->m_aProblems;
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