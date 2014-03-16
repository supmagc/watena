<?php

class AdminModuleItem extends Object {
	
	private $m_oModule;
	private $m_sName;
	private $m_sCategory;
	private $m_sDescription;
	private $m_sMapping;
	private $m_sDefaultModuleTabName;
	private $m_sDefaultModuleTabMapping;
	private $m_oDefaultModuleTabObject = null;
	private $m_aModuleTabs = array();
	
	public function __construct(AdminModule $oModule, $sName, $sCategory, $sDescription, $sDefaultTab) {
		$this->m_oModule = $oModule;
		$this->m_sName = $sName;
		$this->m_sCategory = $sCategory;
		$this->m_sDescription = $sDescription;
		$this->m_sMapping = AdminModuleLoader::convertToMapping($sName);
		$this->m_sDefaultModuleTabName = $sDefaultTab;
		$this->m_sDefaultModuleTabMapping = $this->m_sMapping . AdminModuleLoader::convertToMapping($sDefaultTab);
	}
	
	public function getModule() {
		return $this->m_oModule;
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getCategory() {
		return $this->m_sCategory;
	}
	
	public function getMapping() {
		return $this->m_sMapping;
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function getDefaultModuleTabName() {
		return $this->m_sDefaultModuleTabName;
	}

	public function getDefaultModuleTabMapping() {
		return $this->m_sDefaultModuleTabMapping;
	}
	
	public function getDefaultModuleTab() {
		return $this->m_oDefaultModuleTabObject ?: false;
	}
	
	public function addModuleTab(AdminModuleTab $oModuleTab) {
		$this->m_aModuleTabs[$oModuleTab->getMapping()] = $oModuleTab;
		if($this->m_sDefaultModuleTabName == $oModuleTab->getName()) {
			$this->m_oDefaultModuleTabObject = $oModuleTab;
		}
	}
	
	public function getModuleTabByName($sName) {
		$sMapping = AdminModuleLoader::convertToMapping($sName);
		return $this->getModuleTabByMapping($sMapping);
	}

	public function getModuleTabByMapping($sMapping) {
		return isset($this->m_aModuleTabs[$sMapping]) ? $this->m_aModuleTabs[$sMapping] : false;
	}
	
	public function getModuleTabs() {
		return $this->m_aModuleTabs;
	}
}

?>