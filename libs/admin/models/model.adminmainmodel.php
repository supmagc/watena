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
		$oAjax = new AJAX_Client(null);

		$oRequest = new AJAX_Request('/admin/ajax', 'requestContent');
		$oAjax->registerRequest($oRequest);
		
		$oRequest = new AJAX_Request('/admin/ajax', 'requestLogin');
		$oAjax->registerRequest($oRequest);
		
		$oRequest = new AJAX_Request('/admin/ajax', 'alertHelloWorld');
		$oAjax->registerRequest($oRequest);

		$oRequest = new AJAX_Request('/admin/ajax', 'tester');
		$oAjax->registerRequest($oRequest);
		
		return $oAjax->getOutput();
	}
}

?>