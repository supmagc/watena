<?php
require_plugin('TemplateLoader');

class AdminModuleData extends Object implements IPCO_IContentParser {
	
	private $m_sContent;
	
	public function __construct() {
		
	}
	
	public function getMapping($nIndex, $mDefault = null) {
		
	}
	
	public function setError($sTitle, $sError) {
		
	}
	
	public function setTitle($sTitle) {
		
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
	
	public function parseContent(&$sContent) {
		return array();
	}
} 

?>