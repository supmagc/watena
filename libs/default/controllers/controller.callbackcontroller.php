<?php

class CallbackController extends Controller {
	
	private $m_oCallback;
	private $m_oModel;
	private $m_oView;
	
	public final function process(Model $oModel = null, View $oView = null) {
		$this->m_oCallback = Callback::loadFromRequest();
		$this->m_oModel = $oModel;
		$this->m_oView = $oView;
		
		if(!empty($this->m_oCallback)) {
			$this->getLogger()->info('Callback-data loaded {object}::{method)({arguments})', array('object' => $this, 'method' => $this->m_oCallback->getMethod(), 'arguments' => $this->m_oCallback->getArguments()));
			try {
				$mResult = $this->m_oCallback->process($this);
				if($oModel instanceof ResultModel) {
					$oModel->setResult($mResult);
				}
			}
			catch(Exception $oException) {
				if($oModel instanceof IResult) {
					$oModel->setException($oException);
				}
				else {
					throw $oException;
				}
			}
		}
		else {
			$this->getLogger()->error('CallbackController is unable to trigger any callback when no callback-data could be loaded.');
		}
	}
	
	public final function getCallback() {
		return $this->m_oCallback;
	}
	
	public final function tester() {
		if($this->m_oModel instanceof ResultModel) {
			return 'Hello Callback !';
		}
		else {
			$this->display('Hello Callback !');
		}
	}
}

?>