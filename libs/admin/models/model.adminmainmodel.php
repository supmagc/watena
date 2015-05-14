<?php
require_model('HtmlModel');
require_plugin('Admin');
require_plugin('Ajax');
require_plugin('JQuery');

class AdminMainModel extends HtmlModel {
	
	
	public function init() {
		Events::registerEventCallback(HtmlModel::EVENT_PREPAREHTMLMODEL, array($this, 'onPrepareHtmlModel'));
		
		JQuery::requireJQuery();
		JQuery::requireJQueryUI();
	}
	
	public function onPrepareHtmlModel(HtmlModel $oModel) {
		$oModel->addCssLink('theme/admin/css/admin.main.css');
		$oModel->addCssLink('theme/admin/css/admin.overlay.css');
		$oModel->addJavascriptLink('theme/admin/js/watena-admin.js');
	}
		
	public function getMenus() {
		return Admin::getLoader()->getMenus();
	}
	
	public function getCategories() {
		return Admin::getLoader()->getCategories();
	}
	
	public function getAjax() {
		$oAjax = new AJAX_Client(null);

		$oRequest = new AJAX_Request('/admin/ajax', 'requestInit');
		$oAjax->registerRequest($oRequest);
		
		$oRequest = new AJAX_Request('/admin/ajax', 'requestContent');
		$oAjax->registerRequest($oRequest);
		
		$oRequest = new AJAX_Request('/admin/ajax', 'requestLogin');
		$oAjax->registerRequest($oRequest);

		$oRequest = new AJAX_Request('/admin/ajax', 'requestLogout');
		$oAjax->registerRequest($oRequest);
		
		return $oAjax->getOutput();
	}
}

?>