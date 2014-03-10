<?php

class AdminModuleItem extends Object {
	
	private $m_sName;
	private $m_sCategory;
	private $m_sDescription;
	private $m_sDefaultTab;
	private $m_oDefaultTab;
	private $m_aModuleTabs;
	
	public function __construct($sName, $sCategory, $sDescription, $sDefaultTab) {
		$this->m_sName = $sName;
		$this->m_sCategory = $sCategory;
		$this->m_sDescription = $sDescription;
		$this->m_sDefaultTab = Encoding::toLower($sDefaultTab);
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getCategory() {
		return $this->m_sCategory;
	}
	
	public function getMapping() {
		return '/' . Encoding::toLower($this->getName());
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function getDefaultTabMapping() {
		return $this->m_sDefaultTab;
	}
	
	public function getDefaultModuleTab() {
		return $this->m_oDefaultTab ?: false;
	}
	
	public function addModuleTab(AdminModuleTab $oTab) {
		$this->m_aModuleTabs[$oTab->getName()] = $oTab;
		if($this->m_sDefaultTab == Encoding::toLower($oTab->getName())) {
			$this->m_oDefaultTab = $oTab;
		}
	}
	
	public function getModuleTab($sMapping) {
		$sMapping = Encoding::toLower($sMapping);
		return isset($this->m_aModuleTabs[$sMapping]) ? $this->m_aModuleTabs[$sMapping] : null;
	}
	
	public function getModuleTabs() {
		return $this->m_aModuleTabs;
	}
}

?>