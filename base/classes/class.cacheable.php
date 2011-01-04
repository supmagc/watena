<?php

abstract class Cacheable extends Object {
	
	const EXP_DEFAULT = 0;
	const EXP_NEVER = -1;
	const EXP_REFRESH = -2;
	
	/**
	 * This method is called when initting the object and should leave the object in a cacheable/serializeable state.
	 */
	public function init() {}
	
	/**
	 * This method is called when waking the obhect when loading it back from the cache.
	 * For example: creating a database connection should be done at this time.
	 */
	public function wakeup() {}
	
	/**
	 * This method provides the possibility to autodetermine required modules etc.
	 * The format is an associative array as follows:
	 * 'extension' => Required PHP-Extensions (This call uses 'dl' when available)
	 * 'plugins' => Required Watena-plugins
	 * 'pear' => Required pear installs 
	 */
	public function getRequirements() {return null;}
	
	private static $m_oCreateFunction = null; 
	
	public final function __construct(array $aConfig = array()) {
		parent::__construct($aConfig);
		$this->init();
	}
	
	public final function __wakeup() {
		$this->wakeup();
	}
	
	public static final function create($sObject, array $aConfig = array(), $sIdentifier = null, $nExpirationSec = Cacheable::EXP_DEFAULT, $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		// Generate an identifier if none is given
		if(!$sIdentifier) $sIdentifier = $sObject . count($aPermanentConfig) . implode('', array_keys($aPermanentConfig)) . implode('', array_values($aPermanentConfig));

		// Set expiration if defaulr is given
		if($nExpirationSec == Cacheable::EXP_DEFAULT) $nExpirationSec = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		if($nExpirationSec == Cacheable::EXP_NEVER) $nExpirationSec = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		if($nExpirationSec == Cacheable::EXP_DEFAULT) $nExpirationSec = parent::getWatena()->getConfig('CACHE_EXPIRATION', 5);
		
		// If the loader function is not yet created, do so
		// Params are ass follows:
		// $a classname
		// $b permanentconfig
		// $c inclusionfile
		// $d inherits
		// $e implements
		// $f contextobject
		if(!self::$m_oCreateFunction) {self::$m_oCreateFunction = create_function('$a, $b, $c, $d, $e, $f', '
			if($c) {
				if(file_exists($c)) include_once($c);
				else $f->terminate("Unable to include unexisting file $c.");
			}
			if(!class_exists($a, false)) $f->terminate("The class $a could not be found.");
			if(!in_array("Cacheable", class_parents($a)))	$f->terminate("The class $a does not extends Cacheable.");
			if($d && !in_array($d, class_parents($a)))		$f->terminate("The class $a needs to extend $d.");
			if($e && !in_array($e, class_implements($a)))	$f->terminate("The class $a needs to implement $e.");
			$oTmp = new $a($b);
			$aIncludes = $c ? array($c) : array();
			$aExtensionLoads = array();
			$aPluginLoads = array();
			if(!$f->checkRequirements($oTmp->getRequirements(), true, $aIncludes, $aExtensionLoads, $aPluginLoads))  $f->terminate("The class $a doesn\'t has the right includes.");
			return array($aIncludes, $aExtensionLoads, $aPluginLoads, serialize($oTmp));
		');}
		
		// Retrieve the object from the cache
		list($aIncludes, $aExtensionLoads, $aPluginLoads, $sObject) = parent::getWatena()->getCache()->retrieve(
			$sIdentifier, 
			self::$m_oCreateFunction, 
			$nExpirationSec, 
			array($sObject, $aConfig, $sIncludeFile, $sExtends, $sImplements, parent::getWatena()->getContext()));
			
		// Check all the returnvalues, and load all dependencies
		foreach($aIncludes as $sInclude) require_once($sInclude);
		foreach($aExtensionLoads as $sExtension) dl($sExtension);
		foreach($aPluginLoads as $sPlugin) parent::getWatena()->getContext()->loadPlugin($sPlugin);
		
		// If all succeeded, unserialize the object and return it
		return unserialize($sObject);
	}
}

?>