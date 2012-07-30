<?php

class CacheLoader extends Object {
	
	private $m_aConfig;
	private $m_sClassName;
	private $m_sCustomIdentifier;
	private $m_nLastChanged;
	private $m_sIdentifier;
	
	public final function __construct($sClassName, array $aConfig = array(), $sIdentifier = null) {
		if(!class_exists($sClassName, false)) {
			$this->getLogger()->error('The class \'{class}\' you want to load CacheLoader does not exists.', array('class' => $sClassName));			
		}
		else if(in_array('Cacheable', class_parents($sClassName, false))) {
			$this->getLogger()->error('The class \'{class}\' you want to load CacheLoader does not extend Cacheable.', array('class' => $sClassName));			
		}
		else {
			$this->m_aConfig = $aConfig;
			$this->m_sClassName = $sClassName;
			$this->m_sCustomIdentifier = $sIdentifier;
		}
	}
	
	public function getConfig() {
		return $this->m_aConfig;
	}
	
	public function getClassName() {
		return $this->m_sClassName;
	}
	
	public function getLastChanged() {
		return $this->m_nLastChanged;
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
	
	public function get($sClassName, array $aConfig = array(), array $aInstances = array()) {
		$oInstance = null;
		$sIdentifierInstance = "W_CACHE_$sIdentifier_INSTANCE";
		$sIdentifierLastChanged = "W_CACHE_$sIdentifier_LASTCHANGED";
		$nLastChanged = parent::getWatena()->getCache()->get($sIdentifierLastChanged, 0);
		
		if($this->getLastChanged() <= $nLastChanged) {
			$oInstance = parent::getWatena()->getCache()->get($sIndentifierInstance, null);
			if($oInstance) {
				$this->getLogger()->info('CacheLoader loaded an existing version of \'{class}\' as \'{identifier}\'.', array('class' => $this->getClassName(), 'identifier' => $this->getIdentifier()));
				$oInstance->getCacheData()->injectInstances($aInstances);
			}
			else {
				$this->getLogger()->info('CacheLoader was unable to retrieve an existing version of \'{class}\' as \'{identifier}\'.', array('class' => $this->getClassName(), 'identifier' => $this->getIdentifier()));
			}
		}
		
		if(!$oInstance) {
			$this->getLogger()->info('CacheLoader creates a new version of \'{class}\' as \'{identifier}\'.', array('class' => $this->getClassName(), 'identifier' => $this->getIdentifier()));
			$oType = new ReflectionClass($this->getClassName());
			$oData = new CacheData($this->getConfig(), $aInstances, $sIdentifierInstance, $sIdentifierLastChanged);
			$oInstance = $oType->newInstanceArgs(array($oData));
			$oData->update($oInstance);
		}
		
		return $oInstance;
	}
}

?>