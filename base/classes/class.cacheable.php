<?php

abstract class Cacheable extends Object {
	
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
	
	public function __construct(array $aConfig = array()) {
		parent::__construct($aConfig);
		$this->init();
	}
	
	public function __wakeup() {
		$this->wakeup();
	}
	
	public static final function create($sObject, array $aConfig = array(), $sIdentifier = null, $nExpirationSec = 5, $sIncludeFile = null, $sExtends = null, $sImplements = null) {
		if(!$sIdentifier) $sIdentifier = $sObject . count($aConfig) . implode('', array_keys($aConfig)) . implode('', array_values($aConfig));
		if(!self::$m_oCreateFunction) {self::$m_oCreateFunction = create_function('$a, $b, $c, $d, $e, $f', '
			if($c) {
				if(file_exists($c)) include_once($c);
				else $f->terminate("Unable to include unexisting file $c.");
			}
			if(!class_exists($a, false)) $f->terminate("The class $a could not be found.");
			if(!in_array("Cacheable", class_parents($a)))  $f->terminate("The class $a does not extends Cacheable.");
			if($d && !in_array($d, class_parents($a))) $f->terminate("The class $a needs to extend $d.");
			if($e && !in_array($e, class_implements($a))) $f->terminate("The class $a needs to implement $e.");
			$oTmp = new $a($b);
			$aIncludes = $c ? array($c) : array();
			if(!$f->checkRequirements($oTmp->getRequirements(), true, $aIncludes))  $f->terminate("The class $a doesn\'t has the right includes.");
			return array($aIncludes, serialize($oTmp));
		');}
		list($aIncludes, $sObject) = parent::getWatena()->getCache()->retrieve(
			$sIdentifier, 
			self::$m_oCreateFunction, 
			$nExpirationSec, 
			array($sObject, $aConfig, $sIncludeFile, $sExtends, $sImplements, parent::getWatena()->getContext()));
		foreach($aIncludes as $sInclude) require_once($sInclude);
		return unserialize($sObject);
	}
}

?>