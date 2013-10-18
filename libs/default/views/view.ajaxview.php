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
			$sMessage = "Error while processing your request:\n";
			foreach($oModel->getErrors() as $sError) {
				$sMessage .= "$sError\n";
			}
			$sMessage = rawurlencode($sMessage);
			echo "alert(decodeURIComponent('$sMessage'));";
		}
		else {
			$oModel->generateAjax();
		}
	}
}

?>