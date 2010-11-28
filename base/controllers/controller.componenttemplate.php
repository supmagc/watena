<?php

class ComponentTemplate extends Controller {

	private $m_sTemplate;
	private $m_sComponent;
	
	public function init() {
		$this->m_sTemplate = parent::getConfig('template', null);
		$this->m_sComponent = parent::getConfig('component', null);
	}
	
	public function render() {
		if($this->m_sTemplate && $this->m_sComponent) {
			$oComponentLoader = parent::getWatena()->getContext()->getPlugin('ComponentLoader');
			echo $oComponentLoader->load($this->m_sTemplate);
		}
	}
}

?>