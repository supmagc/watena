<?php

class Configurable extends Object {
	
	private $m_aConfig;
	
	public function __construct(array $aConfig) {
		parent::__construct();
		$this->m_aConfig = $aConfig;
		$sHostKey = strtoupper($_SERVER['HTTP_HOST']); // Don't use Encoding, since it might not be inited yet
		if(isset($this->m_aConfig[$sHostKey])) $this->m_aConfig = array_merge($this->m_aConfig, $this->m_aConfig[$sHostKey]);
		$this->m_aConfig = array_change_key_case($this->m_aConfig, CASE_UPPER);
	}

	public final function getConfig($sKey, $mDefault = null) {
		$sKey = strtoupper($sKey); // Don't use Encoding, since it might not be inited yet
		return isset($this->m_aConfig[$sKey]) ? $this->m_aConfig[$sKey] : $mDefault;
	}
	
	public final function getConfiguration() {
		return $this->m_aConfig;
	}
}

?>