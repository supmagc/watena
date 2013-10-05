<?php
require_plugin('ajax');

class AjaxController extends Controller {
	
	public function process(Model $oModel = null, View $oView = null) {
		if(!is_a($oModel, 'AjaxModel')) {
			$this->getLogger()->error('You need to use an AjaxModel  instead of \'{model}\' when using the AjaxController.', array('model' => $oModel, 'view' => $oView));
		}
		else if(!is_a($oView, 'AjaxView')) {
			$this->getLogger()->error('You need to use an AjaxView  instead of \'{view}\' when using the AjaxController.', array('model' => $oModel, 'view' => $oView));
		}
		else {
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
	}
}

?>