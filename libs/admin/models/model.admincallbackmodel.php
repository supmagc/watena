<?php
require_model('CallbackModel');

class AdminCallbackModel extends Model implements IResult {
	
	private $m_oException;
	private $m_sResult;
	
	public function displayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '') {
		$this->m_sResult .= $this->makeDisplayLogin($sUserName, $sUserNameError, $sPasswordError)->callFunction();
	}

	public function displayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= $this->makeDisplayError($sMessage, $sTitle, $oCallback)->callFunction();
	}

	public function displaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= $this->makeDisplaySucces($sMessage, $sTitle, $oCallback)->callFunction();
	}

	public function displayModuleTabs(AdminModuleTab $oTab) {
		$this->m_sResult .= $this->makeDisplayModuleTabs($oTab)->callFunction();
	}

	public function displayModuleInfo(AdminModuleItem $oItem) {
		$this->m_sResult .= $this->makeDisplayModuleInfo($oItem)->callFunction();
	}
	
	public function displayModuleContent(AdminModuleContent $oContent) {
		$this->m_sResult .= $this->makeDisplayModuleContent($oContent)->callFunction();
	}
	
	public function makeDisplayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '') {
		return new JSFunction('displayLogin', array($sUserName, $sUserNameError, $sPasswordError));
	}
	
	public function makeDisplayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displayError', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getFunction()));
	}

	public function makeDisplaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displaySucces', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getFunction()));
	}

	public function makeDisplayModuleTabs(AdminModuleTab $oTab) {
		$aTabs = array();
		foreach($oTab->getModuleItem()->getModuleTabs() as $oItemTab) {
			$aTabs []= array(
				'name' => $oItemTab->getName(),
				'mapping' => $oItemTab->getMapping(),
				'description' => $oItemTab->getDescription(),
			);
		}
		return new JSFunction('displayModuleTabs', array($oTab->getName(), $aTabs));
	}

	public function makeDisplayModuleInfo(AdminModuleItem $oItem) {
		return new JSFunction('displayModuleInfo', array('', '', ''));
	}
	
	public function makeDisplayModuleContent(AdminModuleContent $oContent) {
		return new JSFunction('displayModuleContent', array('TEST CONTENT TITLE', ''));
	}
	
	public function makeRequestLoadingContent($sMapping) {
		return new JSFunction('requestLoadingContent', array($sMapping));
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