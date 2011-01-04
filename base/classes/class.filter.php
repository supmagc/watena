<?php

class FilterGroup extends Object {
	
	private $m_sFile;
	private $m_sName;
	private $m_aParams = array();
	
	public function __construct($sName, $sType) {
		$this->m_sName = $sName;
		$this->m_sFile = PATH_BASE . '/' . Encoding::stringToLower($sType) . 's/' . Encoding::stringToLower($sType) . '.' . Encoding::stringToLower($sName) . '.php';

		if(!file_exists($this->m_sFile)) parent::terminate('The specified '.$sType.'-file could not be found: ' . $this->m_sFile);
		require_once $this->m_sFile;
		if(!class_exists($this->m_sController, false)) parent::terminate('The specified '.$sType.'-class could not be found: ' . $this->m_sController);
		if(!in_array($sType, class_parents($this->m_sController, false))) parent::terminate('The specified '.$sType.'-class does not implement '.$sType.': ' . $this->m_sController);
	}
	
	public function addParam($sName, $sValue) {
		$this->m_aParams[$sName] = $sValue;
	}
	
	public function getFile() {
		return $this->m_sFile;
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getParams() {
		return $this->m_aParams;
	}
}

class Filter extends Cacheable {

	private $m_sName = '';
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	private $m_nOrder = 0;
	private $m_aRules = array();
	private $m_aParams = array();
	
	public function init() {
		$this->m_oModel = new FilterGroup();
		$this->m_oView = new FilterGroup();
		$this->m_oController = new FilterGroup();
		
		$oXml = new XMLReader();
		$oLast = null;
		if($oXml->XML(parent::getConfig('file', null))) {
			while($oXml->read()) {
				
				if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'filter') {
					$nCount = $oXml->attributeCount;
					for($i=0 ; $i<$nCount ; ++$i) {
						$oXml->moveToAttributeNo($i);
						if($oXml->name == 'name') $this->m_sName = $oXml->value;
						else if($oXml->name == 'order') $this->m_nOrder = (int)$oXml->value;
					}
				}
				else if(($sName == $this->_matchesGetName($oXml, 'model')) !== null) {
					$oLast = $this->m_oModel = new FilterGroup($sName, '');
				}
				else if(($sName == $this->_matchesGetName($oXml, 'view')) !== null) {
					$oLast = $this->m_oView = new FilterGroup($sName, '');
				}
				else if(($sName == $this->_matchesGetName($oXml, 'controller')) !== null) {
					$oLast = $this->m_oController = new FilterGroup($sName, '');
				}
				else if(($sName == $this->_matchesGetName($oXml, 'param')) !== null) {
					$oXml->read();
					$oLast->addParam($sName, $oXml->readString());
				}
				else if(($sName == $this->_matchesGetName($oXml, 'rule')) !== null) {
					$oXml->read();
					$this->m_aRules[$sName] = $oXml->readString();
				}
			}
			if(count($this->m_aRules) == 0) parent::terminate('You need at least one rule in each filter.');
		}
		else {
			parent::terminate('Unable to Parse XML-filter definition: ' . $sData);
		}
	}
	
	public function getModel() {
		return Cacheable::create($this->m_oModel->getName(), $this->m_oModel->getParams(), 'W_MODEL_'.$this->m_oModel->getName(), 5, $this->m_oModel->getFile(), 'Model');
	}
	
	public function getView() {
		return Cacheable::create($this->m_oView->getName(), $this->m_oView->getParams(), 'W_VIEW_'.$this->m_oView->getName(), 5, $this->m_oView->getFile(), 'View');
	}
	
	public function getController() {
		return Cacheable::create($this->m_oController->getName(), $this->m_oController->getParams(), 'W_CONTROLLER_'.$this->m_oController->getName(), 5, $this->m_oController->getFile(), 'Controller');
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getOrder() {
		return $this->m_nOrder;
	}
	
	public function match(Mapping $oMapping) {
		$bSucces = true;
		foreach($this->m_aRules as $sVariable => $sRegex) {
			if(!($bSucces = $bSucces && Encoding::regMatch($sRegex, $oMapping->getVariable($sVariable)))) break;
		}
		return $bSucces;
	}
	
	private function _matchesGetName(XMLReader $oXml, $sMatch) {
		return $oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'rule' && $oXml->moveToAttribute('variable') ? $oXml->value : null;
	}
}

?>