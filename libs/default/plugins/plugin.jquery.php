<?php

class JQuery extends Plugin {
	
	private $m_sLinkJQueryJs;
	private $m_sLinkJQueryUiJs;
	private $m_sLinkJQueryUiCss;
	
	public function make(array $aMembers) {
		$sJqueryVersion = array_value($aMembers, array('jquery-version'), '1.11.1');
		$sJQueryUiVersion = array_value($aMembers, array('jqueryui-version'), '1.11.2');
		$sJqueryUiTheme = array_value($aMembers, array('jqueryui-theme'), 'smoothness');
	}
	
	public function init() {
		Events::registerEventCallback('prepareHtmlModel', array($this, 'onPrepareHtmlModel'));
	}
	
	private function _onPrepareHtmlModel(HtmlModel $oModel) {
		
	}
}