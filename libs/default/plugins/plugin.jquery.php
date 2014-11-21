<?php

class JQuery extends Plugin {
	
	private static $s_sLinkJQueryJs;
	private static $s_sLinkJQueryUiJs;
	private static $s_sLinkJQueryUiCss;
	
	public function make(array $aMembers) {
		$sJqueryVersion = array_value($aMembers, array('jquery-version'), '1.11.1');
		$sJQueryUiVersion = array_value($aMembers, array('jqueryui-version'), '1.11.2');
		$sJqueryUiTheme = array_value($aMembers, array('jqueryui-theme'), 'smoothness');
	}
	
	public function init() {
		Events::registerEventCallback('prepareHtmlModel', array($this, 'onPrepareHtmlModel'));
	}
	
	private function onPrepareHtmlModel(HtmlModel $oModel) {
		if(self::$s_sLinkJQueryJs) $oModel->addJavascriptLink(self::$s_sLinkJQueryJs, true);
		if(self::$s_sLinkJQueryUiJs) $oModel->addJavascriptLink(self::$s_sLinkJQueryUiJs, true);
		if(self::$s_sLinkJQueryUiCss) $oModel->addCssLink(self::$s_sLinkJQueryUiCss, true, true);
	}
	
	public static function requireJQuery($sVersion) {
		
	}
	
	public static function requireJQueryUI($sVersion, $sTheme) {
		
	}
}