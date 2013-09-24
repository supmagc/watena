<?php

/*
class _FilterData extends Object {
	
	const TYPE_UNKNOWN = 0;
	const TYPE_MODEL = 1;
	const TYPE_VIEW = 2;
	const TYPE_CONTROLLER = 3;
	
	private $m_sType;
	private $m_sName;
	private $m_sFileName;
	private $m_sFilePath;
	private $m_aParams = array();
	
	public function __construct($sName, $nType) {
		$this->m_sName = $sName;
		$this->m_nType = $nType;
	}
	
	public function addParam($sName, $sValue) {
		$this->m_aParams[$sName] = $sValue;
	}
	
	public function getType() {
		return $this->m_nType;
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getParams() {
		return $this->m_aParams;
	}
	
	public function create() {
		switch($this->getType()) {
			case self::TYPE_MODEL: return Watena::getWatena()->getContext()->loadModel($this->getName(), $this->getParams());
			case self::TYPE_VIEW: return Watena::getWatena()->getContext()->loadView($this->getName(), $this->getParams());
			case self::TYPE_CONTROLLER:return Watena::getWatena()->getContext()->loadController($this->getName(), $this->getParams());
			default: return null;
		}
	}
}
*/

class Filter extends CacheableFile {

	private $m_sName = '';
	private $m_nOrder = 0;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	private $m_aRules = array();
	
	public function make(array $aMembers) {
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
					$oLast = ($this->m_oModel = new FilterData($sName));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'view')) !== false) {
					$oLast = ($this->m_oView = new FilterData($sName));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'controller')) !== false) {
					$oLast = ($this->m_oController = new FilterData($sName));
				}
				else if(($sName = $this->_matchesGetName($oXml, 'param')) !== false) {
					$oXml->read();
					$oLast->addParam($sName, $oXml->readString());
				}
				else if((list($sVariable, $sCondition) = $this->_matchesGetName($oXml, 'rule', array('variable', 'condition'))) !== false) {
					$oXml->read();
					$this->m_aRules []= new FilterRule(explode('.', $sVariable), $sCondition, $oXml->readString());
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
	
	/*
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
	*/
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getOrder() {
		return $this->m_nOrder;
	}
	
	public function GetModelData() {
		return $this->m_oModel;
	}
	
	public function getViewData() {
		return $this->m_oView;
	}
	
	public function getControllerData() {
		return $this->m_oController;
	}
	
	public function getRules() {
		return $this->m_aRules;
	}
	
	/*
	public function match(Mapping $oMapping) {
		$bSucces = true;
		foreach($this->m_aRules as $aRuleData) {
			
			$sType = Encoding::toLower($aRuleData['type']);
			$sVariable = $aRuleData['variable'];
			$sRegex = $aRuleData['pattern'];
			$sTarget = false;
			
			if($sType == 'get' && isset($_GET[$sVariable])) $sTarget = $_GET[$sVariable];
			else if($sType == 'post' && isset($_POST[$sVariable])) $sTarget = $_POST[$sVariable];
			else if($sType == 'cookie' && isset($_COOKIE[$sVariable])) $sTarget = $_COOKIE[$sVariable];
			else if($sType == 'session' && isset($_SESSION[$sVariable])) $sTarget = $_SESSION[$sVariable];
			else if($sType == 'mapping') $sTarget = $oMapping->getVariable($sVariable); // returns false when not found
			
			if(!($bSucces = $bSucces && $sTarget !== false && Encoding::regMatch($sRegex, $sTarget))) break;
		}
		return $bSucces;
	}
	*/
	
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
	}
}

?>