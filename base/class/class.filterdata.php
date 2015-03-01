<?php

class FilterData extends Object {
	
	private $m_sName;
	private $m_aParams = array();
	
	public final function __construct($sName) {
		$this->m_sName = $sName;
	}
	
	public final function addParam($sName, $sValue) {
		$this->m_aParams[$sName] = $sValue;
	}
	
	public final function getName() {
		return $this->m_sName;
	}
	
	public final function getParams() {
		return $this->m_aParams;
	}
}
