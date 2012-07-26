<?php

class CacheLoader extends Object {
	
	private $m_sClassName;
	private $m_nLastChanged;
	
	public final function __construct($sClassName, $sIdentifier = null) {
		if(!class_exists($sClassName, false)) {
			$this->getLogger()->error('The class \'{class}\' you want to load CacheLoader does not exists.', array('class' => $sClassName));			
		}
		else if(in_array('Cacheable', class_parents($sClassName, false))) {
			$this->getLogger()->error('The class \'{class}\' you want to load CacheLoader does not extend Cacheable.', array('class' => $sClassName));			
		}
		else {
			$this->m_sClassName = $sClassName;
		}
	}
	
	public function addPathDependency($sPath) {
		$sPath = parent::getWatena()->getPath($sPath);
		if(!file_exists($sPath)) {
			$this->getLogger()->warning('The path-dependency \'{path}\' for the CacheLoader does not exists.', array('path' => $sPath));
		}
		else {
			$this->m_nLastChanged = max($this->m_nLastChanged, filemtime($sPath));
		}
	}
	
	public function get() {
		
	}
}

?>