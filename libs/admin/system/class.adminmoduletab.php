<?php

class AdminModuleTab extends Object {

	private $m_oModuleItem;
	private $m_sName;
	private $m_sDescription;
	private $m_sMapping;
	private $m_oModuleContent;
	
	public function __construct(AdminModuleItem $oModuleItem, $sName, $sDescription, AdminModuleContent $oModuleContent) {
		$this->m_oModuleItem = $oModuleItem;
		$this->m_sName = $sName;
		$this->m_sDescription = $sDescription;
		$this->m_sMapping = $this->m_oModuleItem->getMapping() . AdminModuleLoader::convertToMapping($sName);
		$this->m_oModuleContent = $oModuleContent;
	}
	
	public function getModuleItem() {
		return $this->m_oModuleItem;
	}

	public function getName() {
		return $this->m_sName;
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function getMapping() {
		return $this->m_sMapping;
	}
	
	public function getModuleContent() {
		return $this->m_oModuleContent;
	}
}

?>