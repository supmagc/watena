<?php

class AdminModuleTab extends Object {
	
	private $m_sName;
	private $m_sDescription;
	private $m_sType;
	private $m_sContent;
	
	public function __construct($sName, $sDescription, $sType, $sContent) {
		$this->m_sName = $sName;
		$this->m_sDescription = $sDescription;
		$this->m_sType = $sType;
		$this->m_sContent = $sContent;
	}

	public function getName() {
		return $this->m_sName;
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function getMapping() {
		return '/' . Encoding::toLower($this->getName());
	}
	
	public function getContent() {
		return null;
	}
}

?>