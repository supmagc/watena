<?php

class WatCeption extends Exception {
	
	private $m_sMessage;
	private $m_aData;
	private $m_oContext;
	private $m_oInnerException;
	
	public function __construct($sMessage, array $aData = array(), Object $oContext = null, Exception $oInnerException) {
		parent::__construct('A \'Watena\'-error occured. Contact an administrator if you are unable to continue.', null);
	}
	
	public final function getDebugMessage() {
		return $this->m_sMessage;
	}
	
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