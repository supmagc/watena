<?php

class AdminModule extends Object {
	
	private $m_sName;
	private $m_sVersion;
	private $m_sDescription;
	private $m_aModuleItems = array();
	
	public function __construct($sName, $sVersion, $sDescription) {
		$this->m_sName = $sName;
		$this->m_sVersion = $sVersion;
		$this->m_sDescription = $sDescription;
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getVersion() {
		return $this->m_sVersion;
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function addModuleItem(AdminModuleItem $oModuleItem) {
		$this->m_aModuleItems[$oModuleItem->getMapping()] = $oModuleItem;
	}
	
	public function getModuleItemByName($sName) {
		$sMapping = AdminModuleLoader::convertToMapping($sName);
		return $this->getModuleItemByMapping($sMapping);
	}
	
	public function getModuleItemByMapping($sMapping) {
		return isset($this->m_aModuleItems[$sMapping]) ? $this->m_aModuleItems[$sMapping] : false;
	}
	
	public function getModuleItems() {
		return $this->m_aModuleItems;
	}
}

?>