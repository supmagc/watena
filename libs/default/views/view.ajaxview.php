<?php

class AjaxView extends View {
	
	public function headers(Model $oModel = null) {
		$oModel->headers();
	}
	
	public function render(Model $oModel = null) {
		
		if(!$oModel)
		$this->getLogger()->error('No model/data was given.');
		
		if(!$this->m_sMethod)
			$this->getLogger()->error('No method-parameter was given in the view-config.');
					
		if(!method_exists($oModel, $this->m_sMethod))
			$this->getLogger()->error('Unable to find the required method "{method}" in the given Model-object.', array('method' => $this->m_sMethod));
		
		$sContent = json_encode(call_user_func(array($oModel, $this->m_sMethod)));
		echo $sContent;
		
		$aData = array(
			'errors' => $oModel->getErrors()
		);
		echo json_encode($aData);
	}
}

?>