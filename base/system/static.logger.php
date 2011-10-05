<?php

class Logger {
	
	const ALWAYS = 0;
	const TERMINATE = 1;
	const ERROR = 2;
	const WARNING = 3;
	const INFO = 4;
	const EXCEPTION = 5;
	const DEBUG = 6;
	
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
	
	public final function log($nLevel, $sFile, $nLine, $sMessage, array $aData = array(), array $aTrace = array()) {
		if($nLevel <= $this->getFilterLevel()) {
			foreach(self::$s_aProcessors as $oProcessor) {
				$oProcessor->process($this->getIdentifier(), $nLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
			}
		}
	}
	
	/*
	public final function debug($sMessage, $aData = array()) {
		$this->log(self::DEBUG, 1, $sMessage, $aData);
	}
	
	public final function info($sMessage, $aData = array()) {
		$this->log(self::INFO, 1, $sMessage, $aData);
	}
	
	public final function warning($sMessage, $aData = array()) {
		$this->log(self::WARNING, 1, $sMessage, $aData);
	}
	
	public final function error($sMessage, $aData = array()) {
		$this->log(self::ERROR, 1, $sMessage, $aData);
	}
	
	public final function exception(Exception $oException) {
		$this->log(self::EXCEPTION, 0, 'An handled exception occured', array(), $oException);
	}
	
	public final function terminate($sMessage, $aData = array()) {
		$this->log(self::TERMINATE, $sMessage, $aData);
		exit;
	}
	*/
	
	public static final function getInstance($sIdentifier) {
		if(!isset(self::$s_aInstances[$sIdentifier])) {
			self::$s_aInstances[$sIdentifier] = new Logger($sIdentifier);
		}
		return self::$s_aInstances[$sIdentifier];
	}
	
	public static final function processError($nCode, $sMessage, $sFile, $nLine) {
		$oLogger = self::getGenericInstance();
		$aTrace = array_slice(debug_backtrace(), 2);
		switch($nCode) {
			case E_ERROR :
			case E_USER_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_RECOVERABLE_ERROR :
				$oLogger->log(self::ERROR, $sFile, $nLine, $sMessage, array(), $aTrace);
				break;
			case E_WARNING :
			case E_USER_WARNING :
			case E_CORE_WARNING :
			case E_COMPILE_WARNING :
				$oLogger->log(self::WARNING, $sFile, $nLine, $sMessage, array(), $aTrace);
				break;
			case E_NOTICE :
			case E_USER_NOTICE :
			case E_STRICT :
			case E_DEPRECATED :
			case E_USER_DEPRECATED :
				$oLogger->log(self::INFO, $sFile, $nLine, $sMessage, array(), $aTrace);
				break;
		}
		throw new ErrorException($sMessage, 0, $nCode, $sFile, $nLine);
	}
	
	public static final function processException(Exception $oException) {
		if(is_a($oException, 'WatCeption') && $oException->getInnerException() !== null) {
			self::processException($oException->getInnerException());
		}
		$oLogger = null;
		if(is_a($oException, 'WatCeption') && is_a($oException->getContext(), 'Object')) {
			$oObject = $oException->getContext();
			$oLogger = $oObject->getLogger();
		}
		else {
			$oLogger = self::getGenericInstance();
		}
		
		if($oLogger != null) {
			$aTrace = $oException->getTrace();
			if(is_a($oException, 'ErrorException'))
				$aTrace = array_slice($aTrace, 2);
			$oLogger->log(self::TERMINATE, $oException->getFile(), $oException->getLine(), $oException->getMessage(), array(), $aTrace);
		}
		exit; // explicit call this (but shoudn't be needed)
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
	
	public static final function getFileLineTraceLogger($nSteps) {
		// TODO: test this
		++$nSteps;
		$aTrace = array_slice(debug_backtrace(true), $nSteps);
		$aPart = array_shift();
		$sFile = isset($aPart['file']) ? $aPart['file'] : '';
		$nLine = isset($aPart['line']) ? $aPart['line'] : '';
		$oObject = isset($aPart['object']) ? $aPart['object'] : null;
		$sClass = isset($aPart['class']) ? $aPart['class'] : '';
		if(is_a($oObject, 'Object')) {
			$oObject = $oObject->getLogger();
		}
		else if(Encoding::length($sClass) > 0) {
			$oObject = self::getInstance($sClass);
		}
		else {
			$oObject = self::getGenericInstance();
		}
		
		return array($sFile, $nLine, $aTrace, $oObject);
	}
}

?>