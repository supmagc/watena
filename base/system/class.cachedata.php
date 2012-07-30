<?php

class CacheData extends Object {
	
	private $m_aConfig = array();
	private $m_aInstances = array();
	private $m_sIdentifierInstance;
	private $m_sIdentifierLastChanged;
	
	public function __construct(array $aConfig, array $aInstances, $sIdentifierInstance, $sIdentifierLastChanged) {
		$this->m_aConfig = array_change_key_case($aConfig, CASE_LOWER);
		$this->m_aInstances = array_change_key_case($aInstances, CASE_LOWER);
		$this->m_sIdentifierInstance = $sIdentifierInstance;
		$this->m_sIdentifierLastChanged = $sIdentifierLastChanged;
	}
	
	public function __sleep() {
		return array('m_aConfig', 'm_sIdentifierInstance', 'm_sIdentifierLastChanged');
	}
	
	public function getIdentifierInstance() {
		return $this->m_sIdentifierInstance;
	}
	
	public function getIdentifierLastChanged() {
		return $this->m_sIdentifierLastChanged;
	}
	
	public function update(Cacheable $oInstance) {
		parent::getWatena()->getCache()->set($this->getIdentifierInstance(), $oInstance);
		parent::getWatena()->getCache()->set($this->getIdentifierLastChanged(), time());
	}
	
	public function getConfiguration() {
		return $this->m_aConfig;
	}
	
	public function getInstances() {
		return $this->m_aInstances;
	}
	
	public function getConfig($sKey, $mDefault = null) {
		return array_value($this->m_aConfig, explode_trim('.', Encoding::toLower($sKey)), $mDefault);
	}
	
	public function getInstance($sKey, $mDefault = null) {
		return array_value($this->m_aInstances, explode_trim('.', Encoding::toLower($sKey)), $mDefault);
	}
	
	public function injectInstances(array $aInstances) {
		$this->m_aInstances = array_change_key_case($aInstances, CASE_LOWER);
	}
}

?>