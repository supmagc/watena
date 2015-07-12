<?php namespace Watena\Core;
/**
 * Holds all filterdata required to match a filter to a mapping,
 * and knows which model, view, controller to load.
 * 
 * Files are discovered by using FilterGroup, and are parsed with the
 * help of FilterData and FilterRule.
 * 
 * A filter file should be located within a 'filters' directory within a library,
 * and have a name beginning with filter. and ending on .xml
 * 
 * @see Mapping
 * @see FilterData
 * @see FilterRule
 * @see FilterGroup
 * @author Jelle
 * @version 0.2.0
 */
class Filter extends CacheableFile {

	/** @var string */
	private $m_sName = '';
	/** @var int */
	private $m_nOrder = 0;
	/** @var FilterData|null  */
	private $m_oModel = null;
	/** @var FilterData|null  */
	private $m_oView = null;
	/** @var FilterData|null  */
	private $m_oController = null;
	/** @var FilterRule[]  */
	private $m_aRules = array();
	
	/**
	 * Load a filter xml.
	 * 
	 * @see Cacheable::make()
	 */
	public function make(array $aMembers) {
		$oXml = new \SimpleXMLElement(parent::getFilePath(), 0, true);
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
				$aVariable = empty($oRule['variable']) ? 'url' : ('' . $oRule['variable']);
				$sCondition = empty($oRule['condition']) ? 'patern' : ('' . $oRule['condition']);
				if(count($aVariable) > 0) {
					$this->m_aRules []= new FilterRule($aVariable, $sCondition, '' .$oRule);
				}
			}
		}
		
		if(count($this->m_aRules) == 0) throw new WatCeption('You need at least one rule in each filter.', array('filter' => parent::getFilePath()), $this);
		$this->getLogger()->debug('Succesfully parsed filter: {filter}', array(
			'filter' => parent::getFilePath(), 
			'model' => $this->m_oModel != null ? $this->m_oModel->getClass() : 'unknown',
			'view' => $this->m_oView != null ? $this->m_oView->getClass() : 'unknown',
			'controller' => $this->m_oController != null ? $this->m_oController->getClass() : 'unknown'));
	}
	
	/**
	 * Get the name of the filter.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->m_sName;
	}

	/**
	 * Get the priority order of the filter.
	 * 
	 * @return int
	 */
	public function getOrder() {
		return $this->m_nOrder;
	}
	
	/**
	 * Get the data for the Model.
	 * 
	 * @see FilterData
	 * @return FilterData|null
	 */
	public function GetModelData() {
		return $this->m_oModel;
	}
	
	/**
	 * Get the data for the View.
	 * 
	 * @see FilterData
	 * @return FilterData|null
	 */
	public function getViewData() {
		return $this->m_oView;
	}
	
	/**
	 * Get the data for the Controller.
	 * 
	 * @see FilterData
	 * @return FilterData|null
	 */
	public function getControllerData() {
		return $this->m_oController;
	}
	
	/**
	 * Get an array containing the filter rues
	 * 
	 * @see FilterRule
	 * @return FilterRule[]
	 */
	public function getRules() {
		return $this->m_aRules;
	}

	/**
	 * Check if this filter matches the given mapping.
	 * 
	 * @param Mapping $oMapping
	 * @return boolean
	 */
	public final function matches(Mapping $oMapping) {
		foreach($this->m_aRules as $oRule) {
			if(!$oRule->matches($oMapping))
				return false;
		}
		return true;
	}
	
	/**
	 * Helper function to parse model, view, controller data.
	 * 
	 * @param \SimpleXMLElement $oXml
	 * @return FilterData|false
	 */
	private function parseData(\SimpleXMLElement $oXml) {
		$sName = empty($oXml['class']) ? null : '' . $oXml['class'];

		if(!$sName)
			return false;

		$oReturn = new FilterData($sName);
		foreach($oXml->param as $oParam) {
			if(!empty($oParam['name'])) {
				$oReturn->addParam('' . $oParam['name'], '' . $oParam);
			}
		}
		
		return $oReturn;
	}
}
