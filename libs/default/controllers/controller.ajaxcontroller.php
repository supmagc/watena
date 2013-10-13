<?php

class AjaxController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		$oModel->getAjax()->process($this);
	}
}

?>