<?php
require_model('HtmlModel');
require_plugin('Admin');
require_plugin('Ajax');

class AdminMainModel extends HtmlModel {
	
	public function getMenus() {
		return Admin::getLoader()->getMenus();
	}
	
	public function getCategories() {
		return Admin::getLoader()->getCategories();
	}
	
	public function getAjax() {
		$sJavascriptFile = Request::make('/theme/default/js/ajax.js').toString();
		$oAjax = new AJAX_Client($sJavascriptFile);
		
		$oRequest = new AJAX_Request('requestContent');
		$oRequest->setCallback('getContent');
		$oRequest->setUrl('/admin/ajax');
		$oAjax->registerRequest($oRequest);
		
		return $oAjax->getOutput();
	}
}

?>