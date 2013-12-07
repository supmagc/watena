<?php

class JsonView extends View {
	
	public function requiredModelType() {
		return 'IResult';
	}
	
	public function headers(Model $oModel = null) {
		$this->headerContentType('text/json');
	}
	
	public function render(Model $oModel = null) {
		if($oModel->hasException()) {
			echo $oModel->getException();
		}
		else {
			echo json_encode($oModel->getResult());
		}
	}
}

?>