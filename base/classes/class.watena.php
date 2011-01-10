<?php

class Watena extends Object {
	
	private $m_oContext = null;
	private $m_oCache = null;
	private $m_oMapping = null;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	
	public function __construct() {		
		// Load the config-data and overwrite defaults with specific host settings
		$aConfig = parse_ini_file(PATH_BASE . '/watena.ini', true);
		if(!$aConfig) parent::terminate('No readable Watena config file could be found.');
		parent::__construct($aConfig);
		$this->assurePHPSettings();
		
		// Create a new Context and load all required plugins
		$this->m_oCache = new CacheEmpty();
		$this->m_oContext = new Context();
		$sCachePlugin = self::getConfig('CACHE', null);
		if($sCachePlugin) {
			$this->m_oContext->loadPlugin($sCachePlugin);
			$this->m_oCache = $this->m_oContext->GetPlugin($sCachePlugin, 'ICache');
		}
		$this->m_oContext->loadPlugins(array_map('trim', explode(',', parent::getConfig('PLUGINS', ''))));		
		
		// Load the mapping and retrieve the appropriate controller
		$this->m_oMapping = new Mapping();
		list($this->m_oModel, $this->m_oView, $this->m_oController) = $this->m_oContext->getMVC($this->m_oMapping);
		
		$this->m_oController->process($this->m_oModel, $this->m_oView);
		$this->m_oView->render($this->m_oModel);
		
		/*if($this->m_oController) $this->m_oController->render();
		else parent::terminate('No valid controller could be loaded for the given mapping.');
		*/
	}

	/**
	 * Retrieve the application context
	 * 
	 * @return Context
	 */
	public final function getContext() {
		return $this->m_oContext;
	}
	
	public final function getPath($sPath) {
		$aMatches = array();
		if(preg_match('%^([brd]):(/?)(.*?)(/?)$%i', $sPath, $aMatches)) {
			switch($aMatches[1]) {
				case 'b' :
				case 'B' : return PATH_BASE . (strlen($aMatches[3]) > 0 ? "/$aMatches[3]" : '');
				case 'd' :
				case 'D' : return PATH_DATA . (strlen($aMatches[3]) > 0 ? "/$aMatches[3]" : '');
				case 'r' :
				case 'R' : return PATH_ROOT . (strlen($aMatches[3]) > 0 ? "/$aMatches[3]" : '');
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
	public final function assurePHPSettings() {
		set_include_path(get_include_path() . PATH_SEPARATOR . str_replace(',', PATH_SEPARATOR, self::getConfig('INCLUDE', '')));
		Encoding::init(self::getConfig('CHARSET', 'UTF-8'));
		ini_set('default_charset', self::getConfig('CHARSET', 'UTF-8'));
		ini_set('date.timezone', self::getConfig('TIMEZONE', 'UTC'));
		ini_set('error_reporting', E_ALL);
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