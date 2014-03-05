<?php
require_model('CallbackModel');

class AdminCallbackModel extends Model implements IResult {
	
	private $m_oException;
	private $m_sResult;
	
	public function displayLogin() {
		$this->m_sResult = 'displayLogin();';
	}

	public function displayError($sMessage, $sTitle) {
		$this->m_sResult = 'displayError();';
	}

	public function displayInfo($sMessage, $sTitle) {
		$this->m_sResult = 'displayError();';
	}

	public function displaySucces($sMessage, $sTitle) {
		$this->m_sResult = 'displayError();';
	}
	
	public function displayContent(AdminContent $oContent) {
		
	}
	
	public function getResult() {
		return $this->m_sResult;
	}
	
	public function hasException() {
		return !empty($this->m_oException);
	}
	
	public function setException(Exception $oException) {
		$this->m_oException = $oException;
	}
	
	public function getException() {
		return $this->m_oException;
	}
	
	public function formatCall($sFunction, array $aParams) {
		$sReturn = $sFunction;
		$sReturn .= '(';
		$sReturn .= ')';
		return $sReturn;
	}
}

?>