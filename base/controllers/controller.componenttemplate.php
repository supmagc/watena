<?php

class ComponentTemplate extends Controller {

	private $m_sMainComponent;
	
	public function __construct(array $aParams) {
		$this->m_sMainComponent = isset($aParams['component']) ? $aParams['component'] : null;
	}
	
	public function render() {
		if($this->m_sMainComponent) {
			$oComponentLoader = parent::getWatena()->getContext()->getPlugin('ComponentLoader');
			echo $oComponentLoader->load($this->m_sMainComponent);
		}
	}
}

?>