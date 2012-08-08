<?php

class AjaxView extends View {
	
	public function headers(Model $oModel = null) {
		
	}
	
	public function render(Model $oModel = null) {
		$aData = array(
			'errors' => $oModel->getErrors()
		);
		echo json_encode($aData);
	}
}

?>