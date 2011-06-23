<?php

class _FilterData extends Object {
	
	private $m_sType;
	private $m_sName;
	private $m_sFileName;
	private $m_sFilePath;
	private $m_aParams = array();
	
	/**
	 * Create a new FilterGroup (Model/View/Controller)
	 * 
	 * @param string $sName The name of the class we require
	 * @param string $sType Typename (Model/View/Controller)
	 */
	public function __construct($sName, $sType, $sDirectory) {
		$this->m_sType = $sType;
		$this->m_sName = $sName;
		$this->m_sFileName = Encoding::toLower($sType) . '.' . Encoding::toLower($sName) . '.php';
		$this->m_sFilePath = parent::getWatena()->getContext()->getLibraryFilePath($sDirectory, $this->m_sFileName);
		//$this->m_sFile = PATH_BASE . '/' . Encoding::toLower($sType) . 's/' . Encoding::toLower($sType) . '.' . Encoding::toLower($sName) . '.php';

		if($this->m_sFilePath === false) throw new WatCeption('The specified '.$sType.'-file could not be found.', array('name' => $this->m_sName, 'filename' => $this->m_sFileName), $this);
	}
	
	public function addParam($sName, $sValue) {
		$this->m_aParams[$sName] = $sValue;
	}
	
	public function setFirst($bFirst) {
		$this->m_bFirst = $bFirst;
	}
	
	public function setLast($bLast) {
		$this->m_bLast = $bLast;
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
	
	public function create() {
		return CacheableData::createObject($this->m_sName, $this->m_aParams, null, $this->m_sFilePath, $this->m_sType);
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
					}
				}
				else if(($sName = $this->_matchesGetName($oXml, 'model')) !== false) {
					$oLast = ($this->m_oModel = new _FilterData($sName, 'Model', 'models'));
					$oLast->setFirst((bool)($oXml->moveToAttribute('first') && $oXml->value));
					$oLast->setLast((bool)($oXml->moveToAttribute('last') && $oXml->value));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'view')) !== false) {
					$oLast = ($this->m_oView = new _FilterData($sName, 'View', 'views'));
					$oLast->setFirst((bool)($oXml->moveToAttribute('first') && $oXml->value));
					$oLast->setLast((bool)($oXml->moveToAttribute('last') && $oXml->value));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'controller')) !== false) {
					$oLast = ($this->m_oController = new _FilterData($sName, 'Controller', 'controllers'));
					$oLast->setFirst((bool)($oXml->moveToAttribute('first') && $oXml->value));
					$oLast->setLast((bool)($oXml->moveToAttribute('last') && $oXml->value));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'param')) !== false) {
					$oXml->read();
					$oLast->addParam($sName, $oXml->readString());
				}
				else if(($sName = $this->_matchesGetName($oXml, 'rule', 'variable')) !== false) {
					$oXml->read();
					$this->m_aRules[$sName] = $oXml->readString();
				}
			}
			if(count($this->m_aRules) == 0) throw new WatCeption('You need at least one rule in each filter.', array('filter' => parent::getFilePath()), $this);
		}
		else {
			throw new WatCeption('Unable to Parse XML-filter definition.', array('data' => $sData), $this);
		}
	}
	
	public function createModel() {
		return $this->m_oModel ? $this->m_oModel->create() : null;
	}
	
	public function createView() {
		return $this->m_oView ? $this->m_oView->create() : null;
	}
	
	public function createController() {
		return $this->m_oController ? $this->m_oController->create() : null;
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
		return $oXml->nodeType == XMLReader::ELEMENT && $oXml->name == $sMatch && $oXml->moveToAttribute($sNameTag) ? $oXml->value : false;
	}
}

?>