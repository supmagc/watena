<?php

class Watena extends Object {
	
	private $m_aConfig = false;
	private $m_oContext = null;
	private $m_oCache = null;
	private $m_oMapping = null;
	private $m_oController = null;
	
	public function __construct() {
		parent::__construct();
		
		// Load the config-data and overwrite defaults with specific host settings
		$sHostKey = strtoupper($_SERVER['HTTP_HOST']);
		$this->m_aConfig = parse_ini_file(PATH_BASE . '/watena.ini', true);
		if(!$this->m_aConfig) parent::terminate('No readable Watena config file could be found.');
		if(isset($this->m_aConfig[$sHostKey])) $this->m_aConfig = array_merge($this->m_aConfig, $this->m_aConfig[$sHostKey]);

		$this->assurePHPSettings();
		
		// Create a new Context and load all required plugins
		$this->m_oCache = new CacheEmpty();
		$this->m_oContext = new Context();
		$sCachePlugin = self::getConfig('CACHE', null);
		if($sCachePlugin) {
			$this->m_oContext->loadPlugin($sCachePlugin);
			$this->m_oCache = $this->m_oContext->GetPlugin($sCachePlugin, 'ICache');
		}
		$this->m_oContext->loadPlugins(array_map('trim', explode(',', $this->m_aConfig['PLUGINS'])));		

		// Load the mapping and retrieve the appropriate controller
		$this->m_oMapping = new Mapping();
		$this->m_oController = $this->m_oContext->matchFilterToController($this->m_oMapping);
		if($this->m_oController) $this->m_oController->render();
		else parent::terminate('No valid controller could be loaded for the given mapping.');
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
		return $sPath;
	}
	
	public final function getMapping() {
		return $this->m_oMapping;
	}
	
	public final function getVersion() {
		return "{$this->m_aConfig['VERSION_NAME']} - {$this->m_aConfig['VERSION_MAJOR']}.{$this->m_aConfig['VERSION_MINOR']}.{$this->m_aConfig['VERSION_BUILD']} ({$this->m_aConfig['VERSION_STATE']})";
	}

	public final function getConfig($sKey, $mDefault = null) {
		return isset($this->m_aConfig[$sKey]) ? $this->m_aConfig[$sKey] : $mDefault;
	}
	
	public final function assurePHPSettings() {
		set_include_path(get_include_path() . PATH_SEPARATOR . str_replace(',', PATH_SEPARATOR, self::getConfig('INCLUDE', '')));
		Encoding::init(self::getConfig('CHARSET', 'UTF-8'));
		ini_set('default_charset', self::getConfig('CHARSET', 'UTF-8'));
		ini_set('date.timezone', 'Europe/London');
	}
	
	public final function getCache() {
		return $this->m_oCache;
	}

	public final function getController() {
		return $this->m_oController;
	}
}

?>