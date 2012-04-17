<?php

class JsonView extends View {
	
	private $m_sMethod;
	
	public function init() {
		$this->m_sMethod = $this->getConfig('method', false);
	}
	
	public function render(Model $oModel = null) {
		
		if(!$oModel)
		$this->getLogger()->error('No model/data was given.');
		
		if(!$this->m_sMethod)
			$this->getLogger()->error('No method-parameter was given in the view-config.');
					
		if(!method_exists($oModel, $this->m_sMethod))
			$this->getLogger()->error('Unable to find the required method "{method}" in the given Model-object.', array('method' => $this->m_sMethod));
		
		$sContent = json_encode(call_user_func(array($oModel, $this->m_sMethod)));
		if(!headers_sent()) {
			//header('Content-Type: text/'.(headers_sent() ? 'html' : 'json').';charset=' . $oModel->getCharset());
		}
		echo $sContent;
	}
}

?>