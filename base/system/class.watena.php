<?php

class Watena extends Configurable {
	
	private $m_oContext = null;
	private $m_oCache = null;
	private $m_oMapping = null;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	
	public function __construct($aConfig) {	
		ob_start();
		$nTime = microtime(true);	
		parent::__construct($aConfig);
		$this->assureEnvironment();
		
		// Create a default context and default cache
		$this->m_oCache = new CacheEmpty();
		$this->m_oContext = new Context();
		$this->m_oMapping = new Mapping();
		
		// Load all specified logProcessors
		Logger::setDefaultFilterLevel(self::getConfig('LOGGER_FILTERLEVEL', 'ALWAYS'));
		$aLogProcessors = explode_trim(',', self::getConfig('LOGGER_PROCESSORS', ''));
		foreach($aLogProcessors as $sProcessor) {
			if($this->m_oContext->loadPlugin($sProcessor)) {
				$oProcessor = $this->m_oContext->getPlugin($sProcessor, 'ILogProcessor');
				Logger::registerProcessor($oProcessor);
			}
		}
				
		// Load the specified cachingengine
		$sCachePlugin = self::getConfig('CACHE_ENGINE', null);
		if($sCachePlugin) {
			$this->m_oContext->loadPlugin($sCachePlugin);
			$this->m_oCache = $this->m_oContext->GetPlugin($sCachePlugin, 'ICache');
		}
		
		// Load all default plugins
		$this->m_oContext->loadPlugins(explode_trim(',', self::getConfig('PLUGINS', '')));

		// Log the end of the init
		$this->getLogger()->debug('Watena was succesfully initialised in {time} sec.', array('time' => round(microtime(true) - $nTime, 5)));
		$nTime = microtime(true);
		
		// Load the mapping and retrieve the appropriate controller
		do {
			list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
			if($this->m_oController) {
				$this->m_oController->process($this->m_oModel, $this->m_oView);
				if($this->m_oController->hasRemapping()) {
					$this->m_oMapping = $this->m_oController->getRemapping();
					$bContinue = true;
				}
			}
		}
		while($this->m_oController && $this->m_oController->hasRemapping());
		ob_end_flush();
		if($this->m_oView)
			$this->m_oView->render($this->m_oModel);
		else
			echo "\0";
		
		// Log the end of Watena
		$this->getLogger()->debug('Watena loaded and rendered the page in {time} sec.', array('time' => round(microtime(true) - $nTime, 5)));
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
		$aPositions = array();
		if(Encoding::regFind('^([brdlBRDL]):(/?)(.*?)(/?)$', '' . $sPath, $aMatches, $aPositions)) {
			switch($aMatches[1]) {
				case 'b' :
				case 'B' : $sPath = PATH_BASE . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''); break;
				case 'd' :
				case 'D' : $sPath = PATH_DATA . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''); break;
				case 'r' :
				case 'R' : $sPath = PATH_ROOT . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''); break;
				case 'l' :
				case 'L' : $sPath = PATH_LIBS . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''); break;
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
	 * Sett all required PHP-settings
	 */
	public final function assureEnvironment() {
		set_include_path(get_include_path() . PATH_SEPARATOR . str_replace(',', PATH_SEPARATOR, self::getConfig('INCLUDE', '')));
		Encoding::init(self::getConfig('CHARSET', 'UTF-8'));
		Time::init(self::getConfig('TIME_TIMEZONE', 'UTC'), self::getConfig('TIME_FORMAT', 'Y/m/d H:i:s'));
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