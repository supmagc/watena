<?php

require_once 'componentloader/class.component.php';

class ComponentLoader extends Plugin {

	private $m_sDirectory;
	
	public function init() {
		$this->m_sDirectory = parent::getConfig('DIRECTORY', 'D:components');
	}
	
	public function wakeup() {
		
	}
	
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
	
	public function load($sComponent) {
		//return parent::getWatena()->getCache()->retrieve('CL_'.$sComponent, array($this, '_loadComponentFromFile'), 5, array($sComponent));
	}
}

?>