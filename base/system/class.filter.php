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
		return CacheableData::createObject($this->m_sName, $this->m_aParams, array(), null, $this->m_sFilePath, $this->m_sType);
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
	
	public function make() {
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
				}
				else if(($sName = $this->_matchesGetName($oXml, 'view')) !== false) {
					$oLast = ($this->m_oView = new _FilterData($sName, 'View', 'views'));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'controller')) !== false) {
					$oLast = ($this->m_oController = new _FilterData($sName, 'Controller', 'controllers'));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'param')) !== false) {
					$oXml->read();
					$oLast->addParam($sName, $oXml->readString());
				}
				else if((list($sType, $sVariable) = $this->_matchesGetName($oXml, 'rule', array('type', 'variable'))) !== false) {
					$oXml->read();
					$this->m_aRules []= array('type' => $sType, 'variable' => $sVariable, 'pattern' => $oXml->readString());
				}
			}
			if(count($this->m_aRules) == 0) throw new WatCeption('You need at least one rule in each filter.', array('filter' => parent::getFilePath()), $this);
			$this->getLogger()->debug('Succesfully parsed filter: {filter}', array(
				'filter' => parent::getFilePath(), 
				'model' => $this->m_oModel != null ? $this->m_oModel->getName() : 'unknown', 
				'view' => $this->m_oView != null ? $this->m_oView->getName() : 'unknown', 
				'controller' => $this->m_oController != null ? $this->m_oController->getName() : 'unknown'));
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
		foreach($this->m_aRules as $aRuleData) {
			
			$sType = Encoding::toLower($aRuleData['type']);
			$sVariable = $aRuleData['variable'];
			$sRegex = $aRuleData['pattern'];
			$sTarget = false;
			
			if($sType == 'get' && isset($_GET[$sVarieble])) $sTarget = $_GET[$sVariable];
			else if($sType == 'post' && isset($_POST[$sVarieble])) $sTarget = $_POST[$sVariable];
			else if($sType == 'session' && isset($_SESSION[$sVarieble])) $sTarget = $_SESSION[$sVariable];
			else if($sType == 'mapping') $sTarget = $oMapping->getVariable($sVariable); // returns false when not found
			
			if($sTarget !== false && !($bSucces = $bSucces && Encoding::regMatch($sRegex, $sTarget))) break;
		}
		return $bSucces;
	}
	
	private function _matchesGetName(XMLReader $oXml, $sMatch, $aNameTags = array('name')) {
		if(!is_array($aNameTags)) $aNameTags = array($aNameTags);
		if($oXml->nodeType == XMLReader::ELEMENT && $oXml->name == $sMatch) {
			$aReturn = array();
			foreach($aNameTags as $sNameTag) {
				if($oXml->moveToAttribute($sNameTag)) $aReturn []= $oXml->value;
				else return false;
			}
			return count($aNameTags) > 1 ? $aReturn : $aReturn[0];
		}
		return false;
//		return $oXml->nodeType == XMLReader::ELEMENT && $oXml->name == $sMatch && $oXml->moveToAttribute($sNameTag) ? $oXml->value : false;
	}
}

?>