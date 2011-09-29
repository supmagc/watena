<?php

class Logger {
	
	const ALWAYS = 0;
	const TERMINATE = 1;
	const ERROR = 2;
	const EXCEPTION_UNHANDLED = 3;
	const WARNING = 4;
	const INFO = 5;
	const EXCEPTION_HANDLED = 6;
	const DEBUG = 7;
	
	private $m_sIdentifier;
	private $m_nFilterLevel = self::ERROR;
	
	private static $s_aInstances = array();
	private static $s_aProcessors = array();
	private static $s_oGlobalLogger;
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
	}

	public final function setFilterLevel($nLevel) {
		
	}
	
	public final function getFilterLevel() {
		
	}
	
	public final function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	public final function getProcessors() {
		return isset(self::$s_aProcessors[$this->getIdentifier()]) ? self::$s_aProcessors[$this->getIdentifier()] : array();
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
	
	public final function exceptionUnhandled(Exception $oException) {
		exit;
	}
	
	public final function terminate($sMessage, $aData = array()) {
		exit;
	}
	
	
	public static final function getInstance($sIdentifier) {
		if(!isset(self::$s_aInstances[$sIdentifier])) {
			self::$s_aInstances[$sIdentifier] = new Logger($sIdentifier);
		}
		return self::$s_aInstances[$sIdentifier];
	}
	
	public static final function processError($errno, $errstr, $errfile, $errline) {
		switch($nCode) {
			// TODO: process this to the appropriate loggings
		}
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	
	public static final function processException(Exception $oException) {
		if(is_a($oException, 'WatCeption')) {
			$oObject = $oException->getContext();
			$oObject->getLogger()->exceptionUnhandled($oException);
		}
		else {
			// TODO: generic logger-instance
		}
	}
	
	public static final function init() {
		set_error_handler('Logger::processError');
		set_exception_handler('Logger::processException');
	}
	
	public static final function registerProcessor($oProcessor) {
		self::$s_aProcessors []= $oProcessor;
	}
}

?>