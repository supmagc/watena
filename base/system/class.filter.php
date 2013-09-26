<?php

class Filter extends CacheableFile {

	private $m_sName = '';
	private $m_nOrder = 0;
	private $m_oModel = null;
	private $m_oView = null;
	private $m_oController = null;
	private $m_aRules = array();
	
	public function make(array $aMembers) {
		$oXml = new SimpleXMLElement(parent::getFilePath(), 0, true);
		if(!empty($oXml['name'])) {
			$this->m_sName = '' . $oXml['name'];
		}
		if(!empty($oXml['order'])) {
			$this->m_nOrder = (int)$oXml['order'];
		}
		
		if(!empty($oXml->model)) {
			$this->m_oModel = $this->parseData($oXml->model);
		}
		if(!empty($oXml->view)) {
			$this->m_oView = $this->parseData($oXml->view);
		}
		if(!empty($oXml->controller)) {
			$this->m_oController = $this->parseData($oXml->controller);
		}
		if(!empty($oXml->rules)) {
			foreach($oXml->rules->rule as $oRule) {
				$aVariable = empty($oXml->rules->rule['variable']) ? array() : explode('.', $oXml->rules->rule['variable']);
				$sCondition = empty($oXml->rules->rule['condition']) ? 'patern' : ('' . $oXml->rules->rule['condition']);
				if(count($aVariable) > 0) {
					$this->m_aRules []= new FilterRule($aVariable, $sCondition, '' .$oXml->rules->rule);
				}
			}
		}
		
		if(count($this->m_aRules) == 0) throw new WatCeption('You need at least one rule in each filter.', array('filter' => parent::getFilePath()), $this);
		$this->getLogger()->debug('Succesfully parsed filter: {filter}', array(
			'filter' => parent::getFilePath(), 
			'model' => $this->m_oModel != null ? $this->m_oModel->getName() : 'unknown', 
			'view' => $this->m_oView != null ? $this->m_oView->getName() : 'unknown', 
			'controller' => $this->m_oController != null ? $this->m_oController->getName() : 'unknown'));
	}
	
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
	
	private function parseData(SimpleXMLElement $oXml) {
		if(empty($oXml['name'])) return;
		
		$oReturn = new FilterData('' . $oXml['name']);
		foreach($oXml->param as $oParam) {
			if(!empty($oParam['name'])) {
				$oReturn->addParam('' . $oParam['name'], '' . $oParam);
			}
		}
		
		return $oReturn;
	}
}

?>