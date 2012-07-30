<?php
DEPR();

class Configurable extends Object {
	
	private $m_aConfig;
	
	public function __construct(array $aConfig) {
		parent::__construct();
		$this->m_aConfig = $aConfig;
		$this->m_aConfig = array_change_key_case($this->m_aConfig, CASE_UPPER);
	}

	public final function getConfig($sKey, $mDefault = null) {
		// TODO: can this be fixed ?
		$sKey = Encoding::toUpper($sKey); // strtoupper($sKey); // Don't use Encoding, since it might not be inited yet
		return isset($this->m_aConfig[$sKey]) ? $this->m_aConfig[$sKey] : $mDefault;
	}
	
	public final function hasConfig($sKey) {
		// TODO: can this be fixed ?
		$sKey = strtoupper($sKey); // Don't use Encoding, since it might not be inited yet
		return isset($this->m_aConfig[$sKey]);
	}
	
	public final function getConfiguration() {
		return $this->m_aConfig;
	}
}

?>