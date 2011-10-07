<?php

class Logger {
	
	const ALWAYS = 0;
	const DEBUG = 1;
	const EXCEPTION = 2;
	const INFO = 3;
	const WARNING = 4;
	const ERROR = 5;
	const TERMINATE = 6;
	
	const GENERIC_IDENTIFIER = 'GLOBAL';
	
	private $m_sIdentifier;
	private $m_nFilterLevel;
	
	private static $s_aInstances = array();
	private static $s_aProcessors = array();
	private static $s_oGenericLogger;
	private static $s_nDefaultFilterLevel = self::ERROR;
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
		$this->m_nFilterLevel = 0; //self::$s_nDefaultFilterLevel;
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
	
	private final function logCall($nLevel, $sMessage, array $aData = array()) {
		$aTrace = array_slice(debug_backtrace(false), 1);
		$aPart = array_shift($aTrace);
		$this->logFull($nLevel, $aPart['file'], $aPart['line'], $sMessage, $aData, $aTrace);
	}
	
	private final function logFull($nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array()) {
		if($nLevel >= $this->getFilterLevel()) {
			foreach(self::$s_aProcessors as $oProcessor) {
				$oProcessor->process($this->getIdentifier(), $nLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
			}
		}
	}
	
	public final function debug($sMessage, $aData = array()) {
		$this->logCall(self::DEBUG, $sMessage, $aData);
	}
	
	public final function info($sMessage, $aData = array()) {
		$this->logCall(self::INFO, $sMessage, $aData);
	}
	
	public final function warning($sMessage, $aData = array()) {
		$this->logCall(self::WARNING, $sMessage, $aData);
	}
	
	public final function error($sMessage, $aData = array()) {
		$this->logCall(self::ERROR, $sMessage, $aData);
	}
	
	public final function exception(Exception $oException) {
		//$this->log(self::EXCEPTION, 'An handled exception occured', array(), $oException);
	}
	
	public final function terminate($sMessage, $aData = array()) {
		$this->logCall(self::TERMINATE, $sMessage, $aData);
		exit;
	}
	
	public static final function getInstance($sIdentifier) {
		if(!isset(self::$s_aInstances[$sIdentifier])) {
			self::$s_aInstances[$sIdentifier] = new Logger($sIdentifier);
		}
		return self::$s_aInstances[$sIdentifier];
	}
	
	public static final function processError($nCode, $sMessage, $sFile, $nLine) {
		$aTrace = debug_backtrace(false);
		if(in_array($nCode, array(E_USER_NOTICE, E_USER_WARNING, E_USER_ERROR, E_USER_DEPRECATED)))
			array_shift($aTrace);
		$aPart = array_shift($aTrace);
		$sClass = (isset($aTrace[0]) && isset($aTrace[0]['class'])) ? $aTrace[0]['class'] : false;
		if($sClass)
			$oLogger = self::getInstance($sClass);
		else
			$oLogger = self::getGenericInstance();
		
		switch($nCode) {
			case E_ERROR :
			case E_USER_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_RECOVERABLE_ERROR :
				$oLogger->logFull(self::ERROR, $sFile, $nLine, $sMessage, array(), $aTrace);
				break;
			case E_WARNING :
			case E_USER_WARNING :
			case E_CORE_WARNING :
			case E_COMPILE_WARNING :
				$oLogger->logFull(self::WARNING, $sFile, $nLine, $sMessage, array(), $aTrace);
				break;
			case E_NOTICE :
			case E_USER_NOTICE :
			case E_STRICT :
			case E_DEPRECATED :
			case E_USER_DEPRECATED :
				$oLogger->logFull(self::INFO, $sFile, $nLine, $sMessage, array(), $aTrace);
				break;
		}
		throw new ErrorException($sMessage, 0, $nCode, $sFile, $nLine);
	}
	
	public static final function processException(Exception $oException) {
		if(is_a($oException, 'WatCeption') && $oException->getInnerException() !== null) {
			self::processException($oException->getInnerException());
		}
		
		$aTrace = $oException->getTrace();
		if(is_a($oException, 'ErrorException'))
			 array_shift($aTrace);
		
		$aData = array();
		if(is_a($oException, 'WatCeption'))
			$aData = $oException->getData();
		
		if(is_a($oException, 'WatCeption') && is_a($oException->getContext(), 'Object')) {
			$oLogger = $oException->getContext()->getLogger();
		}
		else if(isset($aTrace[0]) && isset($aTrace[0]['class'])) {
			$oLogger = self::getInstance($aTrace[0]['class']);
		}
		else {
			$oLogger = self::getGenericInstance();
		}
		
		$oLogger->logFull(self::TERMINATE, $oException->getFile(), $oException->getLine(), $oException->getMessage(), $aData, $aTrace);
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
	
	public static final function getLevelName($nLevel) {
		switch($nLevel) {
			case self::ALWAYS : return 'ALWAYS';
			case self::DEBUG : return 'DEBUG';
			case self::EXCEPTION : return 'EXCEPTION';
			case self::INFO : return 'INFO';
			case self::WARNING : return 'WARNING';
			case self::ERROR : return 'ERROR';
			case self::TERMINATE : return 'TERMINATE';
			default : return 'UNKNOWN';
		}
	}
}

?>