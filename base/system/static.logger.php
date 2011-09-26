<?php

class Logger {
	
	const ERROR = 0;
	const WARNING = 1;
	const INFO = 2;
	const EXCEPTION = 3;
	const DEBUG = 4;
	
	private $m_sIdentifier;
	
	private static $s_aInstances = array();
	private static $s_aProcessors = array();
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
	}
	
	public final function debug($sMessage, $aData = array()) {
	
	}
	
	public final function exception(Exception $oException) {
	
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
	
	public static function registerLogProcessor($sIdentifier) {
		
	}
}

?>