<?php

class FilterGroup extends CacheableDirectory {
	
	private $m_aFiles = array();
	private $m_aFilters = array();
	
	public function make(array $aMembers) {
		$this->m_aFiles = parent::getFiles('xml', true, 'filter\\.(a-z0-9_)*');
	}
	
	public function init() {
		foreach($this->m_aFiles as $sFile) {
			$oFilter = Filter::create($sFile);
			if(isset($this->m_aFilters[$oFilter->getOrder()])) throw new WatCeption('A filter with this order-number allready exists within this filtergoup.', array(
				'file' => $oFilter->getFilePath(),
				'order' => $oFilter->getOrder(),
				'name' => $oFilter->getName()
			));
			$this->m_aFilters[$oFilter->getOrder()] = $oFilter;
		}
		krsort($this->m_aFilters);
	}
	
	public function getFilters() {
		return $this->m_aFilters;
	}
}
