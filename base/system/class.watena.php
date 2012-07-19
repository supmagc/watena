<?php

class Watena extends Object {
	
	private $m_oContext = null;
	private $m_oCache = null;
	private $m_oMapping = null;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	private $m_oTime = null;
	private $m_oConfig;
	
	public function __construct(WatenaConfig $oConfig, $bUseMvc) {	
		ob_start();
		$nTime = microtime(true);	
		parent::__construct();
		$this->m_oConfig = $oConfig;
		$this->assureEnvironment();
		
		// Create a default context and default cache
		$this->m_oTime = new Time();
		$this->m_oCache = new CacheEmpty();
		$this->m_oContext = new Context();
		$this->m_oMapping = new Mapping();
		
		// Load all specified logProcessors
		Logger::setDefaultFilterLevel($this->getConfig()->getLoggerLevel());
		$aLogProcessors = $this->getConfig()->getLoggerProcessors();
		foreach($aLogProcessors as $sProcessor) {
			if($this->m_oContext->loadPlugin($sProcessor)) {
				$oProcessor = $this->m_oContext->getPlugin($sProcessor, 'ILogProcessor');
				Logger::registerProcessor($oProcessor);
			}
		}
				
		// Load the specified cachingengine
		$sCachePlugin = $this->getConfig()->getCacheEngine();
		if($sCachePlugin) {
			$this->m_oContext->loadPlugin($sCachePlugin);
			$this->m_oCache = $this->m_oContext->GetPlugin($sCachePlugin, 'ICache');
		}

		// Log the end of the init
		$this->getLogger()->debug('Watena was succesfully initialised in {time} sec.', array('time' => round(microtime(true) - $nTime, 5)));
		$nTime = microtime(true);
		
		// Load the mapping and retrieve the appropriate controller
		if($bUseMvc) {
			list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
			while($this->m_oController) {
				$this->m_oController->clearRemapping();
				$this->m_oController->clearNewModel();
				$this->m_oController->clearNewView();
				$this->m_oController->process($this->m_oModel, $this->m_oView);
				if($this->m_oController->hasRemapping()) {
					$this->m_oMapping = $this->m_oController->getRemapping();
					list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
				}
				if($this->m_oController->hasNewModel()) {
					$this->m_oModel = $this->m_oController->getNewModel();
				}
				if($this->m_oController->hasNewView()) {
					$this->m_oView = $this->m_oController->getNewView();
				}
				if(!$this->m_oController->hasRemapping()) {
					break;
				}
			}
			if($this->m_oView) {
				$this->m_oView->headers($this->m_oModel);
			}
			ob_end_flush();
			if($this->m_oView)
				$this->m_oView->render($this->m_oModel);
		
			// Log the end of Watena
			$this->getLogger()->debug('Watena loaded and rendered the page in {time} sec.', array('time' => round(microtime(true) - $nTime, 5)));
		}
	}
	
	/**
	 * @return WatenaConfig
	 */
	public final function getConfig() {
		return $this->m_oConfig;
	}

	/**
	 * Retrieve the application context
	 * 
	 * @return Context
	 */
	public final function getContext() {
		return $this->m_oContext;
	}
	
	/**
	 * Retrieve a valid watena/system path.
	 * The input path should specify a protocol such as:
	 * r (or R) => for Root
	 * d (or D) => for Data
	 * b (or B) => for Base
	 * l (or L) => for Libs
	 * 
	 * @param string $sPath The path to resolve.
	 * @param bool $bVerify Indicate if you cant the path to be verified for existance (retusn false on failure)
	 * @return string|bool
	 */
	public final function getPath($sPath, $bVerify = true) {
		$aMatches = array();
		if(Encoding::regFind('^([brdlBRDL])[:/\\\\]([^:/\\\\].*?)/?$', '' . $sPath, $aMatches)) {
			switch($aMatches[1]) {
				case 'b' :
				case 'B' : $sPath = PATH_BASE . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
				case 'd' :
				case 'D' : $sPath = PATH_DATA . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
				case 'r' :
				case 'R' : $sPath = PATH_ROOT . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
				case 'l' :
				case 'L' : $sPath = PATH_LIBS . (Encoding::length($aMatches[2]) > 0 ? "/$aMatches[2]" : ''); break;
			}
		}
		if($bVerify) {
			return realpath($sPath);
		}
		else {
			$sPath = Encoding::replace('\\', '/', $sPath);	
			$sPath = Encoding::regReplaceAll('/[^/]+/\.\./', '/', $sPath);
			return $sPath;
		}
	}
	
	/**
	 * Retrieve the mapping for the current request.
	 * 
	 * @return Mapping
	 */
	public final function getMapping() {
		return $this->m_oMapping;
	}
	
	/**
	 * String based rpresentation of the version
	 * 
	 * @return string
	 */
	public final function getVersion() {
		return "{$this->m_aConfig['VERSION_NAME']} - {$this->m_aConfig['VERSION_MAJOR']}.{$this->m_aConfig['VERSION_MINOR']}.{$this->m_aConfig['VERSION_BUILD']} ({$this->m_aConfig['VERSION_STATE']})";
	}

	/**
	 * The time object from watena as system time
	 * 
	 * @return Time
	 */
	public final function getTime() {
		return $this->m_oTime;
	}
	
	/**
	 * Sett all required PHP-settings
	 */
	public final function assureEnvironment() {
		//set_include_path(get_include_path() . PATH_SEPARATOR . str_replace(',', PATH_SEPARATOR, self::getConfig('INCLUDE', '')));
		Encoding::init($this->getConfig()->getCharset());
		Time::init($this->getConfig()->getTimeZone(), $this->getConfig()->getTimeFormat());
		if(!is_writable(PATH_DATA)) die('Data path is not writeable.');
	}
	
	/**
	 * Retrieve the caching engine.
	 * 
	 * @return ICache
	 */
	public final function getCache() {
		return $this->m_oCache;
	}

	/**
	 * Retrieve the model part of the MVC
	 * 
	 * @return Model
	 */
	public final function getModel() {
		return $this->m_oModel;
	}

	/**
	 * Retrieve the view part of the MVC
	 * 
	 * @return View
	 */
	public final function getView() {
		return $this->m_oView;
	}

	/**
	 * Return the controller part of the MVC
	 * 
	 * @return Controller
	 */
	public final function getController() {
		return $this->m_oController;
	}
}

?>
