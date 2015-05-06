<?php
/**
 * Controller providing easy extendable callback services.
 * 
 * @author Jelle
 * @version 0.1.0
 */
class CallbackController extends Controller {
	
	private $m_oCallback;
	private $m_oModel;
	private $m_oView;
	
	public final function process(Model $oModel = null, View $oView = null) {
		$this->m_oCallback = Callback::loadFromRequest();
		$this->m_oModel = $oModel;
		$this->m_oView = $oView;
		
		if(!empty($this->m_oCallback)) {
			$this->getLogger()->info('Callback-data loaded {object}::{method)({parameters})', array('object' => $this, 'method' => $this->m_oCallback->getMethod(), 'parameters' => $this->m_oCallback->getParameters()));
			try {
				$mResult = $this->m_oCallback->process($this);
				if(!empty($mResult) && $oModel instanceof ResultModel) {
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
	
	public final function getModel() {
		return $this->m_oModel;
	}
	
	public final function getView() {
		return $this->m_oView;
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
