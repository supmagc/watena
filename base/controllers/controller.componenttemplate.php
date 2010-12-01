<?php

class ComponentTemplate extends Controller {

	private $m_sTemplate;
	private $m_sComponent;
	
	public function getRequirements() {
		return array('plugins' => array('ComponentLoader', 'TemplateLoader'));
	}
	
	public function init() {
		$this->m_sTemplate = parent::getConfig('template', null);
		$this->m_sComponent = parent::getConfig('component', null);
	}
	
	public function render() {
		if($this->m_sTemplate && $this->m_sComponent) {
			$oTemplateLoader = parent::getWatena()->getContext()->getPlugin('TemplateLoader');
			$oComponentLoader = parent::getWatena()->getContext()->getPlugin('ComponentLoader');
			echo $oTemplateLoader->load($this->m_sTemplate);
		}
	}
}

?>