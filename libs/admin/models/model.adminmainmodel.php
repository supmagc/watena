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
		$sJavascriptFile = Request::make('/theme/default/js/ajax.js')->toString();
		$oAjax = new AJAX_Client($sJavascriptFile);
		
		$oRequest = new AJAX_Request('/admin/ajax', 'requestContent');
		$oAjax->registerRequest($oRequest);
		
		$oRequest = new AJAX_Request('/admin/ajax', 'alertHelloWorld');
		$oAjax->registerRequest($oRequest);

		$oRequest = new AJAX_Request('/admin/ajax', 'tester');
		$oRequest->addValue('val', 'Hello World By Value!');
		$oAjax->registerRequest($oRequest);
		
		return $oAjax->getOutput();
	}
}

?>