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
		$oRequest = new AJAX_Request('requestContent');
		$oRequest->setCallback('getContent');
		$oRequest->setUrl(new Mapping('/admin/ajax'));
		$oAjax = new AJAX_Client('file');
		$oAjax->registerRequest($oRequest);
		return $oAjax->getOutput();
	}
}

?>