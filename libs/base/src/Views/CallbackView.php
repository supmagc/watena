<?php namespace Watena\Libs\Base\Views;
use Watena\Core\Model;
use Watena\Core\View;

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
			$aReturn['error_file'] = $oModel->getException()->getFile();
			$aReturn['error_line'] = $oModel->getException()->getLine();
		}
		else if(!empty($mResult)) {
			$aReturn['code'] = 2;
			$aReturn['data'] = $mResult;
		}
		echo json_encode($aReturn);
	}
}
