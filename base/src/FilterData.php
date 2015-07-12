<?php namespace Watena\Core;
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

	private $m_sClass;
	private $m_aParams = array();

	/**
	 * Create a new data group.
	 * (used for Model, View, Controller data)
	 *
	 * @param string $sClass
	 */
	public final function __construct($sClass) {
		$this->m_sClass = $sClass;
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
	 * Get the classname.
	 * This value should be relative to the it's containing library.
	 * 
	 * @return string
	 */
	public final function getClass() {
		return $this->m_sClass;
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
