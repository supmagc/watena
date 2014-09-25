<?php
require_model('CallbackModel');

class AdminCallbackModel extends Model implements IResult {
	
	const CONTENT_TEXT = 0;
	const CONTENT_LOGIN = 1;
	const CONTENT_ERROR = 2;
	const CONTENT_SUCCES = 3;
	
	const REQUEST_MODULETABS = 4;
	const REQUEST_MODULEINFO = 5;
	const REQUEST_MODULECONTENT = 6;
	
	const CLEAR_NAVITEMS = 7;
	const CLEAR_MODULETABS = 8;
	const CLEAR_MODULEINFO = 9;
	const CLEAR_MODULECONTENT = 10;
	
	private $m_oException;
	private $m_sResult;
	
	public function displayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '', $sContinueMapping = '/') {
		$this->m_sResult .= $this->makeDisplayLogin($sUserName, $sUserNameError, $sPasswordError, $sContinueMapping)->getAsCall();
	}

	public function displayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= $this->makeDisplayError($sMessage, $sTitle, $oCallback)->getAsCall();
	}

	public function displaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= $this->makeDisplaySucces($sMessage, $sTitle, $oCallback)->getAsCall();
	}
	
	public function displayNavItems(AdminModuleLoader $oLoader) {
		$this->m_sResult .= $this->makeDisplayNavItems($oLoader)->getAsCall();
	}

	public function displayModuleTabs(AdminModuleContentRequest $oRequest) {
		$this->m_sResult .= $this->makeDisplayModuleTabs($oRequest)->getAsCall();
	}

	public function displayModuleInfo(AdminModuleContentRequest $oRequest) {
		$this->m_sResult .= $this->makeDisplayModuleInfo($oRequest)->getAsCall();
	}
	
	public function displayModuleContent(AdminModuleContentRequest $oRequest) {
		$this->m_sResult .= $this->makeDisplayModuleContent($oRequest)->getAsCall();
	}
	
	public function makeDisplayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '', $sContinueMapping = '/') {
		return new JSFunction('displayLogin', array($sUserName, $sUserNameError, $sPasswordError, $sContinueMapping));
	}
	
	public function makeDisplayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displayError', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getAsDelegate()));
	}

	public function makeDisplaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displaySucces', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getAsDelegate()));
	}
	
	public function makeDisplayNavItems(AdminModuleLoader $oLoader) {
		$aNavs = array();
		foreach($oLoader->getCategories() as $sCategory => $lItems) {
			$aItems = array();
			foreach($lItems as $sItem => $oItem) {
				$aItems []= array(
					'name' => $sItem,
					'mapping' => $oItem->getMapping(),
					'description' => $oItem->getDescription()
				);
			}
			$aNavs []= array(
				'name' => $sCategory,
				'items' => $aItems
			);
		}
		return new JSFunction('displayNavItems', array($aNavs));
	}

	public function makeDisplayModuleTabs(AdminModuleContentRequest $oRequest) {
		$oTab = $oRequest->getTab();
		$aTabs = array();
		foreach($oTab->getModuleItem()->getModuleTabs() as $oItemTab) {
			$aTabs []= array(
				'name' => $oItemTab->getName(),
				'mapping' => $oItemTab->getMapping(),
				'description' => $oItemTab->getDescription(),
			);
		}
		return new JSFunction('displayModuleTabs', array($oTab->getModuleItem()->getName(), $oTab->getModuleItem()->getDescription(), $aTabs));
	}

	public function makeDisplayModuleInfo(AdminModuleContentRequest $oRequest) {
		$oModule = $oRequest->getTab()->getModuleItem()->getModule();
		return new JSFunction('displayModuleInfo', array($oModule->getName(), $oModule->getVersion(), $oModule->getDescription()));
	}
	
	public function makeDisplayModuleContent(AdminModuleContentRequest $oRequest) {
		$oResponse = new AdminModuleContentResponse($this);
		$oRequest->getTab()->getModuleContent()->generate($oRequest, $oResponse);
		$sTitle = $oRequest->getTab()->getName();
		if($oResponse->hasError()) {
			$sTitle .= ': ' . $oResponse->getErrorTitle();
			return new JSFunction('displayModuleContent', array($sTitle, '', $oResponse->getErrorMessage()));
		}
		else {
			if($oResponse->hasTitle())
				$sTitle .= ': ' . $oResponse->getTitle();
			return new JSFunction('displayModuleContent', array($sTitle, $oRequest->getTab()->getDescription(), $oResponse->getContent()));
		}
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