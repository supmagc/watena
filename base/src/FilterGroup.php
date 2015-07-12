<?php namespace Watena\Core;
/**
 * Group all fiters within a folder/library together.
 * This class makes the filter directory cacheable.
 * 
 * @see Filter
 * @see Mapping
 * @see FilterRule
 * @see FilterData
 * @author Jelle
 * @version 0.2.0
 */
class FilterGroup extends CacheableDirectory {
	
	private $m_aFiles = array();
	private $m_aFilters = array();
	
	/**
	 * Create a filtergroup containing all files within a directory
	 * beginning with filter. and ending on .xml.
	 * 
	 * @see Cacheable::make()
	 */
	public function make(array $aMembers) {
		$this->m_aFiles = parent::getFiles('xml', true, 'filter\\.(a-z0-9_)*');
	}

	/**
	 * Load the previously discovered filter.
	 * 
	 * @see Cacheable::init()
	 */
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
	
	/**
	 * Get all filters within this group.
	 * 
	 * @return Filter[]
	 */
	public function getFilters() {
		return $this->m_aFilters;
	}
}
