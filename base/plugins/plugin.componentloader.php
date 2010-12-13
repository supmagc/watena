<?php

class Component extends Cacheable {
	
	private $m_sContent;
	private $m_aComponents;
	private $m_aRegions;
	private $m_aVars;

	public function getVar($sName, $nIndex = 0) {
		
	}
	
	public function toString() {
		return $this->m_sContent;
	}
}

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