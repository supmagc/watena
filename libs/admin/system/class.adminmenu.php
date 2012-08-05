<?php

class AdminMenu extends Object {
	
	private $m_sName;
	private $m_sCategory;
	private $m_sDescription;
	private $m_oDefaultTab;
	private $m_aTabs;
	
	public function __construct($sName, $sCategory, $sDescription, $sDefaultTab, array $aTabs) {
		$this->m_sName = $sName;
		$this->m_sCategory = $sCategory;
		$this->m_sDescription = $sDescription;
		
		$sDefaultTab = Encoding::toLower($sDefaultTab);
		foreach($aTabs as $oTab) {
			$this->m_aTabs[$oTab->getMapping()] = $oTab;
			if($sDefaultTab == $oTab->getMapping())
				$this->m_oDefaultTab = $oTab;
		}
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getCategory() {
		return $this->m_sCategory;
	}
	
	public function getMapping() {
		return '/' . Encoding::toLower($this->getCategory()) . '/' . Encoding::toLower($this->getName());
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function getDefaultTab() {
		return $this->m_oDefaultTab;
	}
	
	public function getTab($sMapping) {
		$sMapping = Encoding::toLower($sMapping);
		return isset($this->m_aTabs[$sMapping]) ? $this->m_aTabs[$sMapping] : null;
	}
	
	public function getTabs() {
		return $this->m_aTabs;
	}
}

?>