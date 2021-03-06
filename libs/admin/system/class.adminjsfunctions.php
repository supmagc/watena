<?php

class AdminJSFunctions extends Object {

	public static function makeSetShowSearch($bShow) {
		return new JSFunction('setShowSearch', array($bShow));
	}
	
	public static function makeSetShowLogout($bShow) {
		return new JSFunction('setShowLogout', array($bShow));
	}
	
	public static function makeDisplayLogin($sUserName = '', $sUserNameError = '', $sPasswordError = '', $sContinueMapping = '/') {
		return new JSFunction('displayLogin', array($sUserName, $sUserNameError, $sPasswordError, $sContinueMapping));
	}
	
	public static function makeDisplayError($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displayError', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getAsDelegate()));
	}
	
	public static function makeDisplaySucces($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displaySucces', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getAsDelegate()));
	}

	public static function makeDisplayConfirm($sMessage, $sTitle, JSFunction $oCallback = null) {
		return new JSFunction('displayConfirm', array($sMessage, $sTitle, empty($oCallback) ? null : $oCallback->getAsDelegate()));
	}
	
	public static function makeClearOverlay() {
		return new JSFunction('clearOverlay');
	}
	
	public static function makeDisplayNavItems(AdminModuleLoader $oLoader) {
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

	public static function makeClearNavItems() {
		return new JSFunction('clearNavItems');
	}
	
	public static function makeDisplayModuleTabs(AdminModuleContentRequest $oRequest) {
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

	public static function makeClearModuleTabs() {
		return new JSFunction('clearModuleTabs');
	}
	
	public static function makeDisplayModuleInfo(AdminModuleContentRequest $oRequest) {
		$oModule = $oRequest->getTab()->getModuleItem()->getModule();
		return new JSFunction('displayModuleInfo', array($oModule->getName(), $oModule->getVersion(), $oModule->getDescription()));
	}

	public static function makeClearModuleInfo() {
		return new JSFunction('clearModuleInfo');
	}
	
	public static function makeDisplayModuleContent(AdminModuleContentRequest $oRequest) {
		$oResponse = new AdminModuleContentResponse();
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

	public static function makeClearModuleContent() {
		return new JSFunction('clearModuleContent');
	}
	
	public static function makeRequestLoadingContent($bInit, $sMapping) {
		return new JSFunction('requestLoadingContent', array($bInit, $sMapping));
	}
	
}

?>