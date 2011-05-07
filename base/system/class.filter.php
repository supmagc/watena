<?php

class FilterGroup extends Object {
	
	private $m_sFile;
	private $m_sName;
	private $m_aParams = array();
	
	/**
	 * Create a new FilterGroup (Model/View/Controller)
	 * 
	 * @param string $sName The name of the class we require
	 * @param string $sType Typename (Model/View/Controller)
	 */
	public function __construct($sName, $sType) {
		$this->m_sName = $sName;
		$this->m_sFile = PATH_BASE . '/' . Encoding::toLower($sType) . 's/' . Encoding::toLower($sType) . '.' . Encoding::toLower($sName) . '.php';

		if(!file_exists($this->m_sFile)) parent::terminate('The specified '.$sType.'-file could not be found: ' . $this->m_sFile);
		require_once $this->m_sFile;
		if(!class_exists($this->m_sName, false)) parent::terminate('The specified '.$sType.'-class could not be found: ' . $this->m_sName);
		if(!in_array($sType, class_parents($this->m_sName, false))) parent::terminate('The specified '.$sType.'-class does not implement '.$sType.': ' . $this->m_sName);
	}
	
	public function addParam($sName, $sValue) {
		$this->m_aParams[$sName] = $sValue;
	}
	
	public function setFinal($bFinal) {
	
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

class Filter extends CacheableFile {

	private $m_sName = '';
	private $m_sTheme = '';
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	private $m_nOrder = 0;
	private $m_aRules = array();
	
	public function init() {
		$oXml = new XMLReader();
		$oLast = null;
		if($oXml->open(parent::getFilepath())) {
			while($oXml->read()) {
				
				if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'filter') {
					$nCount = $oXml->attributeCount;
					for($i=0 ; $i<$nCount ; ++$i) {
						$oXml->moveToAttributeNo($i);
						if($oXml->name == 'name') $this->m_sName = $oXml->value;
						else if($oXml->name == 'order') $this->m_nOrder = (int)$oXml->value;
						else if($oXml->name == 'theme') $this->m_sTheme = (int)$oXml->value;
					}
				}
				else if(($sName = $this->_matchesGetName($oXml, 'model')) !== null) {
					$oLast = ($this->m_oModel = new FilterGroup($sName, 'Model'));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'view')) !== null) {
					$oLast = ($this->m_oView = new FilterGroup($sName, 'View'));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'controller')) !== null) {
					$oLast = ($this->m_oController = new FilterGroup($sName, 'Controller'));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'param')) !== null) {
					$oXml->read();
					$oLast->addParam($sName, $oXml->readString());
				}
				else if(($sName = $this->_matchesGetName($oXml, 'rule', 'variable')) !== null) {
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
		return $this->m_oModel ? CacheableData::createObject($this->m_oModel->getName(), $this->m_oModel->getParams(), null, $this->m_oModel->getFile(), 'Model') : null;
	}
	
	public function getView() {
		return $this->m_oView ? CacheableData::createObject($this->m_oView->getName(), $this->m_oView->getParams(), null, $this->m_oView->getFile(), 'View') : null;
	}
	
	public function getController() {
		return $this->m_oController ? CacheableData::createObject($this->m_oController->getName(), $this->m_oController->getParams(), null, $this->m_oController->getFile(), 'Controller') : null;
	}
	
	public function getTheme() {
		return $this->m_sTheme;
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
	
	private function _matchesGetName(XMLReader $oXml, $sMatch, $sNameTag = 'name') {
		return $oXml->nodeType == XMLReader::ELEMENT && $oXml->name == $sMatch && $oXml->moveToAttribute($sNameTag) ? $oXml->value : null;
	}
}

?>