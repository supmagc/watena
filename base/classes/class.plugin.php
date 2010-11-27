<?php

abstract class Plugin extends Object {

	private $m_aConfig;
	
	public function __construct($aConfig) {
		$this->m_aConfig = $aConfig;
		$this->init();
	}

	public final function getConfig($sKey, $mDefault = null) {
		return isset($this->m_aConfig[$sKey]) ? $this->m_aConfig[$sKey] : $mDefault;
	}
		
	/**
	 * Retrieve version information of this plugin.
	 * The format is an associative array as follows:
	 * 'major' => Major version (int)
	 * 'minor' => Minor version (int)
	 * 'build' => Build version (int)
	 * 'state' => Naming of the production state
	 */
	public abstract function getVersion();

	public abstract function init();
	
	/**
	 * This method provides the possibility to autodetermine required modules etc.
	 * The format is an associative array as follows:
	 * 'extension' => Required PHP-Extensions (This call uses 'dl' when available)
	 * 'plugins' => Required Watena-plugins
	 * 'pear' => Required pear installs 
	 */
	public function getRequirements() {
		return null;
	}
}

?>