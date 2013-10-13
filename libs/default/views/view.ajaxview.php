<?php
require_plugin('ajax');

class AjaxView extends View {
	
	public function headers(Model $oModel = null) {
		$this->setContentType('text/plain');
	}
	
	public function render(Model $oModel = null) {
		if(!is_a($oModel, 'AjaxModel')) {
			
		}
		else if($oModel->hasErrors()) {
			
		}
		else {
			$oModel->generateAjax();
		}
	}
}

?>