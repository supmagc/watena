<?php

class Configurable extends Object {
	
	private $m_aConfig;
	
	public function __construct(array $aConfig) {
		parent::__construct();
		$this->m_aConfig = $aConfig;
		$sHostKey = strtoupper($_SERVER['HTTP_HOST']);
		if(isset($this->m_aConfig[$sHostKey])) $this->m_aConfig = array_merge($this->m_aConfig, $this->m_aConfig[$sHostKey]);
	}

	public final function getConfig($sKey, $mDefault = null) {
		return isset($this->m_aConfig[$sKey]) ? $this->m_aConfig[$sKey] : $mDefault;
	}
}

?>