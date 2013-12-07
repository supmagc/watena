<?php
require_plugin('ajax');

class CallbackView extends View {
	
	public final function requiredModelType() {
		return 'IResult';
	}
	
	public function headers(Model $oModel = null) {
		$this->setContentType('text/json');
	}
	
	public function render(Model $oModel = null) {
		$aReturn = array('code' => 0);
		$mResult = $oModel->getResult();
		if($oModel->hasException()) {
			$aReturn['code'] = 1;
			$aReturn['error_code'] = $oModel->getException()->getCode();
			$aReturn['error_message'] = $oModel->getException()->getMessage();
		}
		else if(!empty($mResult)) {
			$aReturn['code'] = 2;
			$aReturn['data'] = $mResult;
		}
		echo json_encode($aReturn);
	}
}

?>