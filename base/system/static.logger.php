<?php

class Logger {
	
	const INHERIT = -1;
	const ALWAYS = 0;
	const DEBUG = 1;
	const INFO = 2;
	const EXCEPTION = 3;
	const WARNING = 4;
	const ERROR = 5;
	const TERMINATE = 6;
	const NONE = 7;
	
	const GENERIC_IDENTIFIER = 'GLOBAL';
	
	private $m_sIdentifier;
	private $m_nFilterLevel;
	private $m_oFilter;
	
	private static $s_aInstances = array();
	private static $s_aProcessors = array();
	private static $s_oGenericLogger = self::GENERIC_IDENTIFIER;
	private static $s_nDefaultFilterLevel = self::ALWAYS;
	
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
		$this->m_nFilterLevel = self::INHERIT;
	}
	
	public final function setFilter(ILogFilter $oFilter) {
		$this->m_oFilter = $oFilter;
	}
	
	public final function getFilter() {
		return $this->m_oFilter;
	}

	public final function setFilterLevel($nLevel) {
		$this->m_nFilterLevel = $nLevel;
	}
	
	public final function getFilterLevel() {
		return $this->m_nFilterLevel;
	}
	
	public final function approveFilterLevel($nLevel) {
		return $nLevel >= ($this->getFilterLevel() < self::ALWAYS ? self::getDefaultFilterLevel() : $this->getFilterLevel());
	}
	
	public final function getIdentifier() {
		return $this->m_sIdentifier;
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
		if(method_exists($oException, 'getInnerException') && is_a($oException->getInnerException(), 'Exception'))
			$this->exception($oException->getInnerException());
		
		$aData = array();
		if(method_exists($oException, 'getData') && is_array($oException->getData()))
			$aData = $oException->getData();
		
		$this->logFull(self::EXCEPTION, $oException->getFile(), $oException->getLine(), $oException->getMessage(), $aData, $oException->getTrace());
	}
	
	public final function terminate($sMessage, $aData = array()) {
		$this->logCall(self::TERMINATE, $sMessage, $aData);
	}
	
	private final function logCall($nLevel, $sMessage, array $aData) {
		$aTrace = array_slice(debug_backtrace(false), 1);
		$aPart = array_shift($aTrace);
		$this->logFull($nLevel, $aPart['file'], $aPart['line'], $sMessage, $aData, $aTrace);
	}
	
	private final function logFull($nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace) {
		if($this->approveFilterLevel($nLevel)) {
			$bLoggable = true;
			if(is_a($this->getFilter(), 'ILogFilter')) {
				$sCpyIdentifier = $this->getIdentifier();
				$nCpyLevel = $nLevel;
				$bLoggable = $this->getFilter()->loggerFilter($sCpyIdentifier, $nCpyLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
				if($bLoggable) {
					if($sCpyIdentifier != $this->getIdentifier()) {
						return self::getInstance($sCpyIdentifier)->logFull($nCpyLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
					}
					if($nCpyLevel != $nLevel) {
						$bLoggable = $bLoggable && $this->approveFilterLevel($nCpyLevel);
						$nLevel = $nCpyLevel;
					}
				}
			}
			if($bLoggable) {				
				foreach(self::$s_aProcessors as $oProcessor) {
					$oProcessor->loggerProcess($this->getIdentifier(), $nLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
				}
			}
		}
	}
	
	/**
	 * Retrieve the specified logger-instance.
	 * If none exists, create one.
	 * 
	 * @param string $sIdentifier
	 * @return Logger
	 */
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
		if(method_exists($oException, 'getInnerException') && is_a($oException->getInnerException(), 'Exception')) {
			self::processException($oException->getInnerException());
		}
		
		$aTrace = $oException->getTrace();
		if(is_a($oException, 'ErrorException'))
			 array_shift($aTrace);
		
		$aData = array();
		if(method_exists($oException, 'getData') && is_array($oException->getData()))
			$aData = $oException->getData();
		
		if(method_exists($oException, 'getContext') && method_exists($oException->getContext(), 'getLogger') && is_a($oException->getContext()->getLogger(), 'Logger')) {
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
		ini_set('error_reporting', E_ALL);
		set_error_handler('Logger::processError');
		set_exception_handler('Logger::processException');
	}
	
	public static final function getDefaultFilterLevel() {
		return self::$s_nDefaultFilterLevel;
	}
	
	public static final function setDefaultFilterLevel($mLevel) {
		self::$s_nDefaultFilterLevel = is_numeric($mLevel) ? (int)$mLevel : self::getLevelConstant($mLevel);
	}
	
	public static final function registerProcessor($oProcessor) {
		self::$s_aProcessors []= $oProcessor;
	}
	
	public static final function getGenericInstance() {
		return self::getInstance(self::GENERIC_IDENTIFIER);
	}
	
	public static final function getLevelName($nLevel) {
		switch($nLevel) {
			case self::INHERIT : return 'INHERIT';
			case self::ALWAYS : return 'ALWAYS';
			case self::DEBUG : return 'DEBUG';
			case self::EXCEPTION : return 'EXCEPTION';
			case self::INFO : return 'INFO';
			case self::WARNING : return 'WARNING';
			case self::ERROR : return 'ERROR';
			case self::TERMINATE : return 'TERMINATE';
			case self::NONE : return 'NONE';
			default : return 'UNKNOWN';
		}
	}
	
	public static final function getLevelConstant($sLevel) {
		switch($sLevel) {
			case 'INHERIT' : return self::INHERIT;
			case 'ALWAYS' : return self::ALWAYS;
			case 'DEBUG' : return self::DEBUG;
			case 'EXCEPTION' : return self::EXCEPTION;
			case 'INFO' : return self::INFO;
			case 'WARNING' : return self::WARNING;
			case 'ERROR' : return self::ERROR;
			case 'TERMINATE' : return self::TERMINATE;
			default : return self::NONE;
		}
	}
}

?>