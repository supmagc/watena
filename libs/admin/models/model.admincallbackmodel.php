<?php
require_model('CallbackModel');

class AdminCallbackModel extends Model implements IResult {
	
	private $m_oException;
	private $m_sResult;
	
	public function displayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '', $sContinueMapping = '/') {
		$this->m_sResult .= $this->makeDisplayLogin($sUserName, $sUserNameError, $sPasswordError, $sContinueMapping)->callFunction();
	}

	public function displayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= $this->makeDisplayError($sMessage, $sTitle, $oCallback)->callFunction();
	}

	public function displaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		$this->m_sResult .= $this->makeDisplaySucces($sMessage, $sTitle, $oCallback)->callFunction();
	}
	
	public function displayNavItems(AdminModuleLoader $oLoader) {
		$this->m_sResult .= $this->makeDisplayNavItems($oLoader)->callFunction();
	}

	public function displayModuleTabs(AdminModuleTab $oModuleTab) {
		$this->m_sResult .= $this->makeDisplayModuleTabs($oModuleTab)->callFunction();
	}

	public function displayModuleInfo(AdminModuleTab $oModuleTab) {
		$this->m_sResult .= $this->makeDisplayModuleInfo($oModuleTab)->callFunction();
	}

	public function makeDisplayContent(AdminModuleTab $oModuleTab) {
		$this->m_sResult .= $this->makeDisplayModuleContent($oModuleTab)->callFunction();
	}
	
	public function displayModuleContent(AdminModuleTab $oModuleTab) {
		$this->m_sResult .= $this->makeDisplayModuleContent($oModuleTab)->callFunction();
	}
	
	public function makeDisplayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '', $sContinueMapping = '/') {
		return new JSFunction('displayLogin', array($sUserName, $sUserNameError, $sPasswordError, $sContinueMapping));
	}
	
	public function makeDisplayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displayError', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getFunction()));
	}

	public function makeDisplaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displaySucces', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getFunction()));
	}
	
	public function makeDisplayNavItems(AdminModuleLoader $oLoader) {
		$aNavs = array();
		foreach($oLoader->getCategories() as $sCategory => $lSubItems) {
			$aSubItems = array();
			foreach($lSubItems as $sSubItem => $oSubItem) {
				$aSubItems []= array(
					'name' => $sSubItem,
					'mapping' => $oSubItem->getMapping(),
					'description' => $oSubItem->getDescription()
				);
			}
			$aNavs []= array(
				'name' => $sCategory,
				'subitems' => $aSubItems
			);
		}
		return new JSFunction('displayNavItems', array($aNavs));
	}

	public function makeDisplayModuleTabs(AdminModuleTab $oModuleTab) {
		$aTabs = array();
		foreach($oModuleTab->getModuleItem()->getModuleTabs() as $oItemTab) {
			$aTabs []= array(
				'name' => $oItemTab->getName(),
				'mapping' => $oItemTab->getMapping(),
				'description' => $oItemTab->getDescription(),
			);
		}
		return new JSFunction('displayModuleTabs', array($oModuleTab->getName(), $aTabs));
	}

	public function makeDisplayModuleInfo(AdminModuleTab $oModuleTab) {
		$oModule = $oModuleTab->getModuleItem()->getModule();
		return new JSFunction('displayModuleInfo', array($oModule->getName(), $oModule->getVersion(), $oModule->getDescription()));
	}
	
	public function makeDisplayModuleContent(AdminModuleTab $oModuleTab) {
		$oData = new AdminModuleData();
		$oModuleTab->getModuleContent()->generate($oData);
		if($oData->hasError()) {
			return new JSFunction('displayModuleContent', array($oData->getErrorTitle(), $oData->getErrorMessage()));
		}
		else {
			$sTitle = $oModuleTab->getName();
			if($oData->hasTitle())
				$sTitle .= ': ' . $oData->getTitle();
			return new JSFunction('displayModuleContent', array($sTitle, $oData->getContent()));
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