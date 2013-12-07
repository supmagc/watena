<?php

class WatCeption extends Exception {
	
	//private $m_sMessage;
	private $m_aData;
	private $m_oContext;
	private $m_oInnerException;
	
	public function __construct($sMessage, array $aData = array(), Object $oContext = null, Exception $oInnerException = null) {
		parent::__construct($sMessage, null);
		//$this->m_sMessage = $sMessage;
		$this->m_aData = $aData;
		$this->m_oContext = $oContext;
		$this->m_oInnerException = $oInnerException;
	}
	
	/*
	public final function getDebugMessage() {
		return $this->m_sMessage;
	}
	*/
	
	public final function getData() {
		return $this->m_aData;
	} 
	
	public final function getContext() {
		return $this->m_oContext;
	}
	
	public final function getInnerException() {
		return $this->m_oInnerException;
	} 
}

?>