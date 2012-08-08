<?php

class AjaxController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		if(!is_a($oModel, 'AjaxModel')) {
			
		}
		else if(!is_a($oView, 'AjaxView')) {
			
		}
		else if(!isset($_POST['callback'])) {
			
		}
		else if(method_exists($this, $_POST['callback'])) {
			
		}
		else {
			$aParams = isset($_POST['params']) ? json_decode($_POST['params']) : array();
			call_user_method_array($_POST['callback'], $this, $aParams);
		}
	}
}

?>