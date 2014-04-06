<?php
require_plugin('TemplateLoader');

class AdminModuleData extends Object implements IPCO_IContentParser {
	
	private $m_sTitle;
	private $m_sContent;
	private $m_sErrorTitle;
	private $m_sErrorMessage;
	
	public function __construct() {
		
	}
	
	public function getMapping($nIndex, $mDefault = null) {
		
	}
	
	public function setError($sTitle, $sMessage) {
		$this->m_sErrorTitle = $sTitle;
		$this->m_sErrorContent = $sMessage;
	}
	
	public function hasError() {
		return !empty($this->m_sErrorTitle) || !empty($this->m_sErrorMessage);
	}
	
	public function getErrorTitle() {
		return $this->m_sErrorTitle;
	}
	
	public function getErrorMessage() {
		return $this->m_sErrorMessage;
	}
	
	public function setTitle($sTitle) {
		$this->m_sTitle;
	}
	
	public function hasTitle() {
		return !empty($this->m_sTitle);
	}
	
	public function getTitle() {
		return $this->m_sTitle;
	}
	
	public function setContentText($sContent) {
		$this->m_sContent = $sContent;
	}

	public function setContentTemplate($sTemplateName, Model $oModel = null) {
		$oGenerator = TemplateLoader::load($sTemplateName, $this);
		if(!empty($oGenerator))
			$oGenerator->componentPush($oModel);
		
		$this->m_sContent = $oGenerator->generate();
	}
	
	public function hasContent() {
		return !empty($this->m_sContent);
	}
	
	public function getContent() {
		return $this->m_sContent;
	}
	
	public function parseContent(&$sContent) {
		return array();
	}
} 

?>