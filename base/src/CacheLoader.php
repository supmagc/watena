<?php namespace Watena\Core;

class CacheLoader extends Object {
	
	private $m_sExtends = null;
	private $m_aMembers = array();
	private $m_sClassName = null;
	private $m_nLastChanged = 0;
	private $m_aIdentifiers = array();
	
	public function __construct($sClassName, array $aMembers = array(), $sExtends = null) {
		$sCacheable =  __NAMESPACE__.'\Cacheable';
		if(!class_exists($sClassName)) {
			$this->getLogger()->error('CacheLoader cannot load \'{class}\' as the class does not exists.', array('class' => $sClassName));			
		}
		else if(!is_subclass_of($sClassName, $sCacheable) || ($sExtends && !is_subclass_of($sClassName, $sExtends))) {
			$this->getLogger()->error('CacheLoader cannot load \'{class}\' as the class does not extend \'{extends}\' and/or \'{cacheable}\'.', array('class' => $sClassName, 'extends' => $sExtends, 'cacheable' => $sCacheable));
		}
		else {
			$this->m_sClassName = $sClassName;
			$this->m_aMembers = $aMembers;
			$this->m_sExtends = $sExtends;
		}
	}
	
	public function setMembers(array $aMembers) {
		$this->m_aMembers = $aMembers;
	}
	
	public function addMembers(array $aMembers) {
		$this->m_aMembers = array_merge_recursive($this->m_aMembers, $aMembers);
	}
	
	public function addMember($sName, $mValue) {
		$this->m_aMembers[$sName] = $mValue;
	}
	
	public function getMembers() {
		return $this->m_aMembers;
	}
	
	public function getExtends() {
		return $this->m_sExtends;
	}
	
	public function getClassName() {
		return $this->m_sClassName;
	}
	
	public function getLastChanged() {
		return $this->m_nLastChanged;
	}
	
	public function addPathDependencies(array $aPaths) {
		$bReturn = true;
		foreach($aPaths as $sPath) {
			$bReturn = $this->addPathDependency($sPath) && $bReturn;
		}
		return $bReturn;
	}
	
	public function addPathDependency($sPath) {
		$sPath = parent::getWatena()->getPath($sPath);
		if(!file_exists($sPath)) {
			$this->getLogger()->warning('The path-dependency \'{path}\' for the CacheLoader does not exists.', array('path' => $sPath));
			return false;
		}
		else {
			$this->m_aIdentifiers []= $sPath;
			$this->m_nLastChanged = max($this->m_nLastChanged, filemtime($sPath));
			return true;
		}
	}
	
	public function addDataDependency($mData) {
		$this->m_aIdentifiers []= serialize($mData, true);
	}
	
	public function get(array $aConfig = array()) {
		$oInstance = null;
		ksort($this->m_aMembers);
		sort($this->m_aIdentifiers);
		$sIdentifier = md5($this->getClassName() . serialize($this->m_aMembers) . serialize($this->m_aIdentifiers) . serialize(call_user_func(array($this->getClassName(), 'coarseCacheIdentifier'))));
		$sIdentifierInstance = "W_CACHE_{$sIdentifier}_INSTANCE";
		$sIdentifierLastChanged = "W_CACHE_{$sIdentifier}_LASTCHANGED";
		$nLastChanged = parent::getWatena()->getCache()->get($sIdentifierLastChanged, 0);
		
		if($this->getLastChanged() <= $nLastChanged) {
			$oInstance = parent::getWatena()->getCache()->get($sIdentifierInstance, null);
			if($oInstance) {
				$this->getLogger()->info('CacheLoader loaded an existing version of \'{class}\' as \'{identifier}\'.', array('class' => $this->getClassName(), 'identifier' => $sIdentifier));
				$oInstance->getCacheData()->injectConfiguration($aConfig);
			}
			else {
				$this->getLogger()->info('CacheLoader was unable to retrieve an existing version of \'{class}\' as \'{identifier}\'.', array('class' => $this->getClassName(), 'identifier' => $sIdentifier));
			}
		}
		
		if(!$oInstance || !$oInstance->validate()) {
			$oType = new \ReflectionClass($this->getClassName());
			$oData = new CacheData($aConfig, $sIdentifierInstance, $sIdentifierLastChanged);
			$oInstance = $oType->newInstanceArgs(array($oData));
			$this->getLogger()->info('CacheLoader created a new version of \'{class}\' as \'{identifier}\'.', array('class' => $this->getClassName(), 'identifier' => $sIdentifier));
			if($this->GetExtends()) {
				$oType = new \ReflectionClass($this->getExtends());
			}
			while($oType && $oType->getName() != 'Cacheable') {
				$aProperties = $oType->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);
				foreach($aProperties as $oProperty) {
					if(isset($this->m_aMembers[$oProperty->getName()])) {
						$oProperty->setAccessible(true);
						$oProperty->setValue($oInstance, $this->m_aMembers[$oProperty->getName()]);
					}
				}
				$oType = $oType->getParentClass();
			}
			$oInstance->make($this->getMembers());
			$oData->update($oInstance);
		}
		
		if($oInstance)
			$oInstance->init();
		
		return $oInstance;
	}
}
