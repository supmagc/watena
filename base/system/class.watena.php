<?php

class Watena extends Configurable {
	
	private $m_oContext = null;
	private $m_oCache = null;
	private $m_oMapping = null;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	
	public function __construct($aConfig) {		
		parent::__construct($aConfig);
		$this->assureEnvironment();
		
		// Create a new Context and load all required plugins
		$this->m_oCache = new CacheEmpty();
		$this->m_oContext = new Context();
		$aLogProcessors = explode(',', self::getConfig('LOGGER_PROCESSORS', 'EchoLog'));
		foreach($aLogProcessors as $sProcessor) {
			$this->m_oContext->loadPlugin($sProcessor);
			Logger::registerProcessor($this->m_oContext->getPlugin($sProcessor, 'ILogProcessor'));
		}
		Logger::init();
		$this->getLogger()->info('sdliuvh {name} sdfk<br />bvhyu', array('name' =>'Jelle Voet'));
		throw new WatCeption("my bla exception", array('time' => time()), $this->m_oContext);
		$b = $a == true;
		trigger_error('pomoijh', E_USER_ERROR);
		$sCachePlugin = self::getConfig('CACHE_ENGINE', null);
		if($sCachePlugin) {
			$this->m_oContext->loadPlugin($sCachePlugin);
			$this->m_oCache = $this->m_oContext->GetPlugin($sCachePlugin, 'ICache');
		}
		$this->m_oContext->init();
		
		// Load the mapping and retrieve the appropriate controller
		$this->m_oMapping = new Mapping();
		list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
		
		$this->m_oController->process($this->m_oModel, $this->m_oView);
		$this->m_oView->render($this->m_oModel);
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
	 * Retrieve a valid watena path.
	 * The input path should specify a protocol such as:
	 * r (or R) => for Root
	 * d (or D) => for Data
	 * b (or B) => for Base
	 * 
	 * @param string $sPath
	 * @return string
	 */
	public final function getPath($sPath) {
		$aMatches = array();
		$aPositions = array();
		if(Encoding::regFind('^([brd]):(/?)(.*?)(/?)$', '' . $sPath, $aMatches, $aPositions)) {
			switch($aMatches[1]) {
				case 'b' :
				case 'B' : return realpath(PATH_BASE . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''));
				case 'd' :
				case 'D' : return realpath(PATH_DATA . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''));
				case 'r' :
				case 'R' : return realpath(PATH_ROOT . (Encoding::length($aMatches[3]) > 0 ? "/$aMatches[3]" : ''));
			}
		}
		return realpath($sPath);
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
		ini_set('date.timezone', self::getConfig('TIMEZONE', 'UTC'));
		ini_set('error_reporting', E_ALL);
		if(!is_writable(PATH_DATA)) throw new Exception('Data path is not writeable.');
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