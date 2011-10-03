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
	
	const GENERIC_IDENTIFIER = '__GENERIC__';
	
	private $m_sIdentifier;
	private $m_nFilterLevel;
	
	private static $s_aInstances = array();
	private static $s_aProcessors = array();
	private static $s_oGenericLogger;
	private static $s_nDefaultFilterLevel = self::ERROR;
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
		$this->m_nFilterLevel = self::$s_nDefaultFilterLevel;
	}

	public final function setFilterLevel($nLevel) {
		$this->m_nFilterLevel = $nLevel;
	}
	
	public final function getFilterLevel() {
		return $this->m_nFilterLevel;
	}
	
	public final function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	public final function getProcessors() {
		return isset(self::$s_aProcessors[$this->getIdentifier()]) ? self::$s_aProcessors[$this->getIdentifier()] : array();
	}
	
	public final function log($nLevel, $nStrip, $sMessage, array $aData = array(), Exception $oException = null) {
		if($nLevel <= $this->getFilterLevel()) {
			$aTrace = debug_backtrace();
			$sFile = __FILE__;
			$nLine = __LINE__;
			if($nStrip > 0) {
				$aTrace = array_slice($aTrace, $nStrip - 1);
				$aLast = array_shift($aTrace);
				$sFile = $aLast['file'];
				$nLine = $aLast['line'];
			}
			foreach(self::$s_aProcessors as $oProcessor) {
				$oProcessor->process($this->getIdentifier(), $nLevel, $sFile, $nLine, $sMessage, $aData, $oException, $aTrace);
			}
		}
	}
	
	public final function debug($sMessage, $aData = array()) {
		$this->log(self::DEBUG, $sMessage, $aData);
	}
	
	public final function exception(Exception $oException) {
		$this->log(self::EXCEPTION_HANDLED, 'An handled exception occured', array(), $oException);
	}
	
	public final function info($sMessage, $aData = array()) {
		$this->log(self::INFO, $sMessage, $aData);
	}
	
	public final function warning($sMessage, $aData = array()) {
		$this->log(self::WARNING, $sMessage, $aData);
	}
	
	public final function error($sMessage, $aData = array()) {
		$this->log(self::ERROR, $sMessage, $aData);
	}
	
	public final function exceptionUnhandled(Exception $oException) {
		$this->log(self::EXCEPTION_UNHANDLED, 'An unhandled exception occured', array(), $oException);
		exit;
	}
	
	public final function terminate($sMessage, $aData = array()) {
		$this->log(self::TERMINATE, $sMessage, $aData);
		exit;
	}
	
	
	public static final function getInstance($sIdentifier) {
		if(!isset(self::$s_aInstances[$sIdentifier])) {
			self::$s_aInstances[$sIdentifier] = new Logger($sIdentifier);
		}
		return self::$s_aInstances[$sIdentifier];
	}
	
	public static final function processError($nCode, $sMessage, $errfile, $errline) {
		$oLogger = self::getGenericInstance();
		switch($nCode) {
			case E_ERROR :
			case E_USER_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_RECOVERABLE_ERROR :
				$oLogger->log(self::ERROR, 4, $sMessage);
				break;
			case E_WARNING :
			case E_USER_WARNING :
			case E_CORE_WARNING :
			case E_COMPILE_WARNING :
				$oLogger->log(self::WARNING, 4, $sMessage);
				break;
			case E_NOTICE :
			case E_USER_NOTICE :
			case E_STRICT :
			case E_DEPRECATED :
			case E_USER_DEPRECATED :
				$oLogger->log(self::INFO, 4, $sMessage);
				break;
		}
		throw new ErrorException($sMessage, 0, $nCode, $errfile, $errline);
	}
	
	public static final function processException(Exception $oException) {
		if(is_a($oException, 'WatCeption') && is_a($oException->getContext(), 'Object')) {
			$oObject = $oException->getContext();
			$oObject->getLogger()->exceptionUnhandled($oException);
		}
		else {
			self::getGenericInstance()->exceptionUnhandled($oException);
		}
	}
	
	public static final function init() {
		set_error_handler('Logger::processError');
		set_exception_handler('Logger::processException');
	}
	
	public static final function registerProcessor($oProcessor) {
		self::$s_aProcessors []= $oProcessor;
	}
	
	public static final function getGenericInstance() {
		return self::getInstance(self::GENERIC_IDENTIFIER);
	}
}

?>