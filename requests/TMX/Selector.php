<?php

/**
 * Selector TMX class to browse the DOM of the source
 * 
 * @author Jelle Voet - ToMo-design
 * @version 1.0.0 beta
 *
 */
class TMX_Selector {
	
	private $m_aPath = array();
	
	public function __construct($sID = null) {
		if($sID !== null) {
			$this->m_aPath []= array(0, $sID);
		}
	}
	
	/**
	 * Continue selector based on name
	 * 
	 * @param string $sName
	 * @param string $sAttribute (default = null)
	 * @param string $sValue (default = null)
	 * @return TMX_Selector
	 */
	public function Name($sName, $sAttribute = null, $sValue = null) {
		if($sAttribute === null || $sValue === null) $this->m_aPath []= array(1, $sName);
		else $this->m_aPath []= array(1, $sName, $sAttribute, $sValue);
		return $this;
	}
	
	/**
	 * Continue selector based on tag
	 * 
	 * @param string $sName
	 * @param string $sAttribute (default = null)
	 * @param string $sValue (default = null)
	 * @return TMX_Selector
	 */
	public function Tag($sTag, $sAttribute = null, $sValue = null) {
		if($sAttribute === null || $sValue === null) $this->m_aPath []= array(2, $sTag);
		else $this->m_aPath []= array(2, $sTag, $sAttribute, $sValue);
		return $this;
	}
	
	/**
	 * Continue selector on all childs
	 * 
	 * @param string $sAttribute (default = null)
	 * @param string $sValue (default = null)
	 * @return TMX_Selector
	 */
	public function Childs($sAttribute = null, $sValue = null) {
		if($sAttribute === null || $sValue === null) $this->m_aPath []= array(3);
		else $this->m_aPath []= array(3, $sAttribute, $sValue);
		return $this;
	}
	
	/**
	 * Continue selector on the first child
	 * 
	 * @return TMX_Selector
	 */
	public function FirstChild() {
		$this->m_aPath []= array(4);
		return $this;
	}
	
	/**
	 * Continue selector on ths last child
	 * 
	 * @return TMX_Selector
	 */
	public function LastChild() {
		$this->m_aPath []= array(5);
		return $this;
	}
	
	/**
	 * Retrieve the total path
	 * 
	 * @return array
	 */
	public function GetPath() {
		return $this->m_aPath;
	}
	
	/**
	 * Check if the selector is valid
	 * 
	 * @return boolean
	 */
	public function IsValid() {
		return count($this->m_aPath) > 0;
	}
	
	/**
	 * Create a new selector
	 * 
	 * @param string $sID (default = null)
	 * @return TMX_Selector
	 */
	public static function Create($sID = null) {
		return new TMX_Selector($sID);
	}
}

?>