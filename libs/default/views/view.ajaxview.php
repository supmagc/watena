<?php
require_plugin('ajax');

class AjaxView extends View {
	
	public function headers(Model $oModel = null) {
		$this->setContentType('text/plain');
	}
	
	public function render(Model $oModel = null) {
		try {
			$oServer = new AJAX_Server($oModel);
			foreach($oServer->getValues() as $sName => $mValue) {
				$oModel->$sName = $mValue;
			}
			$sCallback = $oServer->getCallback();
			
			if(empty($sCallback)) {
				$this->getLogger()->error('No callback defined to use for the ajax-request.');
			}
			else if(!method_exists($oModel, $sCallback)) {
				$this->getLogger()->error('The defined callback \'{callback}\' is not defined within \'{model}\'.', array('callback' => $oServer->getCallback(), 'model' => $oModel));
			}
			else {
				call_user_func_array(array($oModel, $sCallback), $oServer->getArguments());
			}
		}
		catch(Exception $e) {
			echo 'alert(decodeURIComponent(\''.rawurlencode($e->getMessage()).'\');';
		}
	}
}

?>