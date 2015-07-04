<?php

/**
 * This class manages the logging of watena.
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
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
	private static $s_aLogCounters = array();
	private static $s_oGenericLogger = self::GENERIC_IDENTIFIER;
	private static $s_nDefaultFilterLevel = self::ALWAYS;
	
	/**
	 * Only used internally when no logger with the specified identifier exists.
	 * 
	 * @param string $sIdentifier
	 */
	private final function __construct($sIdentifier) {
		$this->m_sIdentifier = $sIdentifier;
		$this->m_nFilterLevel = self::INHERIT;
	}
	
	/**
	 * Set an optional filter (object implementing ILOgFilter) to verify and adapt
	 * log messages and calls. It to provides the means to change the data  before 
	 * being logged, thus enabling external log-control. 
	 * 
	 * Only one filter for each logger can be active at any given time.
	 * If during the filtering, the filter would be changed, the behaviour is undefined.
	 * 
	 * @param ILogFilter $oFilter
	 */
	public final function setFilter(ILogFilter $oFilter) {
		$this->m_oFilter = $oFilter;
	}
	
	/**
	 * Get the optional ILogFilter object or null when none is set.
	 * 
	 * @return ILogFilter | null
	 */
	public final function getFilter() {
		return $this->m_oFilter;
	}

	/**
	 * Set the filter-level for this logger.
	 * For more information on the available levels, have a look at the class constants.
	 * Defaults to Logger::INHERIT which takes the Logger::getDefaultFilterLevel().
	 * 
	 * @param int $nLevel
	 */
	public final function setFilterLevel($nLevel) {
		$this->m_nFilterLevel = $nLevel;
	}
	
	/**
	 * Get the actual filter-level for this logger.
	 * For more information on the available levels, have a look at the class constants.
	 * 
	 * @return int
	 */
	public final function getFilterLevel() {
		return $this->m_nFilterLevel;
	}
	
	/**
	 * Check if the given filter-level would pass the settings of this logger.
	 * The result is influenced by the getFilterLevel() and possibly
	 * Logger::getDefaultFilterLevel() results.
	 * 
	 * @param int $nLevel
	 * @return bool
	 */
	public final function approveFilterLevel($nLevel) {
		return $nLevel >= ($this->getFilterLevel() < self::ALWAYS ? self::getDefaultFilterLevel() : $this->getFilterLevel());
	}
	
	/**
	 * Get the identifier of thgis logger.
	 * Mostly this represents the class to which the logger is linked,
	 * or it's the name of a custom created logger. (ex: the requirements)
	 * 
	 * @return string
	 */
	public final function getIdentifier() {
		return $this->m_sIdentifier;
	}
	
	/**
	 * Used for debug messages when things get wrong.
	 * These should be extended info messages (both quantity and quality)
	 * A good measure is to use this for messages never used in production.
	 * This means a bugreport not containing these should be sufficient.
	 * 
	 * @param string $sMessage
	 * @param array $aData
	 */
	public final function debug($sMessage, $aData = array()) {
		$this->logCall(self::DEBUG, $sMessage, $aData);
	}

	/**
	 * Used for information messages about the processing of the request.
	 * These should be very specific messages when things get processed.
	 * Based on this information you should be able to trace what happened.
	 * 
	 * @param string $sMessage
	 * @param array $aData
	 */
	public final function info($sMessage, $aData = array()) {
		$this->logCall(self::INFO, $sMessage, $aData);
	}
	
	/**
	 * Used for warning messages when things happen that should be fixed
	 * or clarified. The system is capable to continue working, but the
	 * results might not be what you expected.
	 * Idealy, the message should contain some hints on how to improve.
	 * 
	 * @param string $sMessage
	 * @param array $aData
	 */
	public final function warning($sMessage, $aData = array()) {
		$this->logCall(self::WARNING, $sMessage, $aData);
	}

	/**
	 * Used for error messages when something goes wrong.
	 * Most of the time, the code will continue to run, but you'll
	 * encounter some problems due to this error later on.
	 * You should look into this as soon as possible, and fix it!
	 * 
	 * @param string $sMessage
	 * @param array $aData
	 */
	public final function error($sMessage, $aData = array()) {
		$this->logCall(self::ERROR, $sMessage, $aData);
	}
	
	/**
	 * Used for logging an exception as critical when not being correctly catched.
	 * This might terminate your code when not logged from within a try-catch.
	 * You should mostly only use this with ugly external libraries,
	 * or when you're to lasy to write an approriate try-catch/log output.
	 * 
	 * @param Exception $oException
	 * @param int $nLevel The type of log that will be generated form the exception (default: EXCEPTION)
	 */
	public final function exception(Exception $oException, $nLevel = self::EXCEPTION) {
		if(method_exists($oException, 'getInnerException') && $oException->getInnerException() instanceof Exception)
			$this->exception($oException->getInnerException());
		
		$aData = array();
		if(method_exists($oException, 'getData') && is_array($oException->getData()))
			$aData = $oException->getData();
		
		$this->logFull($nLevel, $oException->getFile(), $oException->getLine(), $oException->getMessage(), $aData, $oException->getTrace());
	}
	
	/**
	 * Used to terminate the system with a logger message.
	 * Call this when to prevent actions that could affect the stability of the system.
	 * 
	 * @param string $sMessage
	 * @param array $aData
	 */
	public final function terminate($sMessage, $aData = array()) {
		$this->logCall(self::TERMINATE, $sMessage, $aData);
	}
	
	/**
	 * Internal function to extract the file and line data from the trace
	 * and dispatches these to logFull().
	 * 
	 * @param int $nLevel
	 * @param string $sMessage
	 * @param array $aData
	 */
	private final function logCall($nLevel, $sMessage, array $aData) {
		$aTrace = array_slice(debug_backtrace(false), 1);
		$aPart = array_shift($aTrace);
		$this->logFull($nLevel, $aPart['file'], $aPart['line'], $sMessage, $aData, $aTrace);
	}
	
	/**
	 * Perform the actual logging. This internal method takes care of the filtering,
	 * the approval, and the dispatching to the ILogProcessors.
	 * If the level is exception or terminate, this call exists the runtime.
	 * 
	 * @param int $nLevel
	 * @param string $sFile
	 * @param int $nLine
	 * @param string $sMessage
	 * @param array $aData
	 * @param array $aTrace
	 */
	private final function logFull($nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace) {
		if(!isset(self::$s_aLogCounters[$nLevel])) {
			self::$s_aLogCounters[$nLevel] = 1;
		}
		else {
			self::$s_aLogCounters[$nLevel] += 1;
		}
		
		if($this->approveFilterLevel($nLevel)) {
			$bLoggable = true;
			if($this->getFilter() instanceof ILogFilter) {
				$sCpyIdentifier = $this->getIdentifier();
				$nCpyLevel = $nLevel;
				$bLoggable = $this->getFilter()->loggerFilter($sCpyIdentifier, $nCpyLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
				if($bLoggable) {
					if($sCpyIdentifier != $this->getIdentifier()) {
						self::getInstance($sCpyIdentifier)->logFull($nCpyLevel, $sFile, $nLine, $sMessage, $aData, $aTrace);
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
			if($nLevel > Logger::ERROR) {
				exit;
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
	
	/**
	 * PHP error catcher.
	 * This will create an exception, based on the error, and throw it.
	 * The design principle here is, make your code safe !
	 * If it doesn't compile, or generates php-errors at runtime
	 * you're doing it wrong and you should look into it!
	 * 
	 * @param int $nCode
	 * @param string $sMessage
	 * @param string $sFile
	 * @param int $nLine
	 * @throws ErrorException
	 */
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

		throw new ErrorException($sMessage, 0, $nCode, $sFile, $nLine);
	}

	/**
	 * PHP exception catcher.
	 * This function will try to redirect the exception to its rightfull owner/logger,
	 * and will try to create a log entry for the provided data.
	 * The log-creation/-processing will be done in all registered ILogProcessor's.
	 * Make sure nothing can go wrong in those, as otherwise the behaviour is undefined.
	 * 
	 * @param Exception $oException
	 */
	public static final function processException(Exception $oException) {
		if(method_exists($oException, 'getInnerException') && $oException->getInnerException() instanceof Exception) {
			self::processException($oException->getInnerException());
		}
		
		$aTrace = $oException->getTrace();
		if($oException instanceof ErrorException)
			 array_shift($aTrace);
		
		$aData = array();
		if(method_exists($oException, 'getData') && is_array($oException->getData()))
			$aData = $oException->getData();
		
		if(method_exists($oException, 'getContext') && method_exists($oException->getContext(), 'getLogger') && $oException->getContext()->getLogger() instanceof Logger) {
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
	
	/**
	 * Initialize the Logger system.
	 * This is not inforced to be called before using anything else, bit is highly
	 * recommended. It sets the error-reporting to cancel out PHP's settings, and it
	 * registers the PHP error and exception catchers and sets the initial default 
	 * filter level.
	 * 
	 * @param int $nDefaultfilterLevel
	 */
	public static final function init($nDefaultfilterLevel) {
		ini_set('error_reporting', E_ALL);
		set_error_handler('Logger::processError');
		set_exception_handler('Logger::processException');
		self::$s_nDefaultFilterLevel = (int)$nDefaultfilterLevel;
	}
	
	/**
	 * Get the default filter level.
	 * This setting is  originally retrieved form the watena-configuration, and is used for
	 * loggers who have their filter-level set to Logger::INHERIT.
	 * 
	 * @return int
	 */
	public static final function getDefaultFilterLevel() {
		return self::$s_nDefaultFilterLevel;
	}
	
	/**
	 * Sets the default filter level.
	 * This can be a integer value (see the class constants) or can be a
	 * string representation of one of the class constants.
	 * 
	 * @param mixed $mLevel
	 */
	public static final function setDefaultFilterLevel($mLevel) {
		self::$s_nDefaultFilterLevel = is_numeric($mLevel) ? (int)$mLevel : self::getLevelConstant($mLevel);
	}
	
	/**
	 * Register a given ILogProcessor.
	 * These will process and dispatch all received logger messages.
	 * 
	 * @param ILogProcessor $oProcessor
	 */
	public static final function registerProcessor(ILogProcessor $oProcessor, $bClearPrevious = false) {
		if($bClearPrevious) {
			self::$s_aProcessors = array();
		}
		self::$s_aProcessors []= $oProcessor;
	}

	/**
	 * Get the generic logger instance.
	 * 
	 * @return Logger
	 */
	public static final function getGenericInstance() {
		return self::getInstance(self::GENERIC_IDENTIFIER);
	}
	
	/**
	 * Get the filter level string representation based on a given class constant.
	 * 
	 * @param int $nLevel
	 * @return string
	 */
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
	
	/**
	 * Get the filter level class constant based on a given string representation.
	 * 
	 * @param string $sLevel
	 * @return int
	 */
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
