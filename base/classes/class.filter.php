<?php

class Filter extends Object {

	private $m_sFile = '';
	private $m_sName = '';
	private $m_sModel = null;
	private $m_sView = null;
	private $m_sController = null;
	private $m_nOrder = 0;
	private $m_aRules = array();
	private $m_aParams = array();
	private $m_aRequires = array();
	
	public function __construct($sData) {
		$oXml = new XMLReader();
		if($oXml->XML($sData)) {
			while($oXml->read()) {
				if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'filter') {
					$nCount = $oXml->attributeCount;
					for($i=0 ; $i<$nCount ; ++$i) {
						$oXml->moveToAttributeNo($i);
						if($oXml->name == 'name') $this->m_sName = $oXml->value;
						else if($oXml->name == 'controller') $this->m_sController = $oXml->value;
						else if($oXml->name == 'order') $this->m_nOrder = (int)$oXml->value;
					}
				}
				else if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'rule' && $oXml->moveToAttribute('variable')) {
					$sValue = $oXml->value;
					$oXml->read();
					$this->m_aRules[$sValue] = $oXml->readString();
				}
				else if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'param' && $oXml->moveToAttribute('name')) {
					$sValue = $oXml->value;
					$oXml->read();
					$this->m_aParams[$sValue] = $oXml->readString();
				}
				else if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == 'plugin') {
					$oXml->read();
					$this->m_aRequires []= $oXml->readString();
				}
			}
			$this->m_sFile = PATH_BASE . '/controllers/controller.' . Encoding::stringToLower($this->m_sController) . '.php';
			if(!file_exists($this->m_sFile)) parent::terminate('The specified controller-file could not be found: ' . $this->m_sFile);
			require_once $this->m_sFile;
			if(!class_exists($this->m_sController, false)) parent::terminate('The specified controller could not be found: ' . $this->m_sController);
			if(!in_array('Controller', class_parents($this->m_sController, false))) parent::terminate('The specified controlller class does not implement Controller: ' . $this->m_sController);
			if(count($this->m_aRules) == 0) parent::terminate('You need at least one rule in each filter.');
			foreach($this->m_aRequires as $sPlugin) if(!parent::getWatena()->getContext()->loadPlugin($sPlugin)) parent::terminate('The required plugin for this filter could not be loaded: ' . $sPlugin . '(' . $this->m_sName . ')');
		}
		else {
			parent::terminate('Unable to Parse XML-filter definition: ' . $sData);
		}
	}
	
	public function __wakeup() {
		foreach($this->m_aRequires as $sPlugin) if(!parent::getWatena()->getContext()->loadPlugin($sPlugin)) parent::terminate("The required plugin for this filter could not be loaded: $sPlugin ($this->m_sName)");
	}
	
	public function getController() {
		return Cacheable::create($this->m_sController, $this->m_aParams, 'W_CONTROLLER_'.$this->m_sController, 5, $this->m_sFile, 'Controller');
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
}

?>