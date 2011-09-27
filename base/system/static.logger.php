<?php

class Logger {
	
	const ALWAYS = 0;
	const ERROR = 1;
	const WARNING = 2;
	const INFO = 3;
	const EXCEPTION = 4;
	const DEBUG = 5;
	
	private $m_sIdentifier;
	
	private static $s_aInstances = array();
	private static $s_aProcessors = array();
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
	}
	
	public final function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	public final function getProcessors() {
		return isset(self::$s_aProcessors[$this->getIdentifier()]) ? self::$s_aProcessors[$this->getIdentifier()] : array();
	}
	
	public final function log($nCode = self::ALWAYS, $sMessage = 'empty log-message', Exception $oException = null, $aData = array()) {
		foreach($this->getProcessors() as $oProcessor) {
			$oProcessor
		}
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
	
	public static function registerProcessor($sIdentifier, $oProcessor) {
		if(!isset(self::$s_aProcessors[$sIdentifier])) {
			self::$s_aProcessors[$sIdentifier] = array();
		}
		self::$s_aProcessors[$sIdentifier] []= $oProcessor;
	}
}

?>