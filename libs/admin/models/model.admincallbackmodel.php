<?php
require_model('CallbackModel');

class AdminCallbackModel extends Model implements IResult {
	
	private $m_oException;
	private $m_sResult;
	
	public function displayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '', $sContinueMapping = '/') {
		$this->m_sResult .= AdminJSFunctions::makeDisplayLogin($sUserName, $sUserNameError, $sPasswordError, $sContinueMapping)->getAsCall();
	}

	public function displayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= AdminJSFunctions::makeDisplayError($sMessage, $sTitle, $oCallback)->getAsCall();
	}

	public function displaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= AdminJSFunctions::makeDisplaySucces($sMessage, $sTitle, $oCallback)->getAsCall();
	}
	
	public function displayNavItems(AdminModuleLoader $oLoader) {
		$this->m_sResult .= AdminJSFunctions::makeDisplayNavItems($oLoader)->getAsCall();
	}

	public function displayModuleTabs(AdminModuleContentRequest $oRequest) {
		$this->m_sResult .= AdminJSFunctions::makeDisplayModuleTabs($oRequest)->getAsCall();
	}

	public function displayModuleInfo(AdminModuleContentRequest $oRequest) {
		$this->m_sResult .= AdminJSFunctions::makeDisplayModuleInfo($oRequest)->getAsCall();
	}
	
	public function displayModuleContent(AdminModuleContentRequest $oRequest) {
		$this->m_sResult .= AdminJSFunctions::makeDisplayModuleContent($oRequest)->getAsCall();
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
}

?>