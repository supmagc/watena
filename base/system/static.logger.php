<?php

class Logger {
	
	private $m_sIdentifier;
	
	private static $s_aInstances = array();
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
	}
	
	public final function debug($sMessage, $aData = array()) {
	
	}
	
	public final function info($sMessage, $aData = array()) {
		
	}
	
	public final function warning($sMessage, $aData = array()) {
	
	}
	
	public final function error($sMessage, $aData = array()) {
	
	}
	
	public static final function getInstance($sIdentifier) {
		if(!isset(self::$s_aInstances[$sIdentifier])) {
			self::$s_aInstances[$sIdentifier] = new Logger($sIdentifier);
		}
		return self::$s_aInstances[$sIdentifier];
	}
}

?>