<?php
/**
 * Helper class for parsing the content of a filter xml.
 * 
 * @see Filter
 * @see Mapping
 * @see FilterRule
 * @see FilterGroup
 * @author Jelle
 * @version 0.2.0
 */
class FilterData extends Object {
	
	private $m_sName;
	private $m_aParams = array();

	/**
	 * Create a new data group.
	 * (used for Model, View, Controller data)
	 * 
	 * @param string $sName
	 */
	public final function __construct($sName) {
		$this->m_sName = $sName;
	}
	
	/**
	 * Add a new parameter to this filterdata.
	 * 
	 * @param string $sName
	 * @param string $sValue
	 */
	public final function addParam($sName, $sValue) {
		$this->m_aParams[$sName] = $sValue;
	}
	
	/**
	 * Get the name.
	 * 
	 * @return string
	 */
	public final function getName() {
		return $this->m_sName;
	}

	/**
	 * Get all parameters.
	 * 
	 * @return string[]
	 */
	public final function getParams() {
		return $this->m_aParams;
	}
}
