<?php

abstract class Controller extends CacheableData {
	
	abstract public function process(Model $oModel = null, View $oView = null);
	public function requiredModelType() {return null;}
	public function requiredViewType() {return null;}
		
	private $m_oRemapping;
	private $m_oModel;
	private $m_oView;
	
	public final function redirect($sLocation) {
		if(Encoding::substring($sLocation, 0, 4) !== 'http') $sLocation = $this->getWatena()->getMapping()->getRoot() . $sLocation;
		header('location: ' . $sLocation, true);
	}
	
	public final function setRemapping($sMapping) {
		return $this->m_oRemapping = new Mapping($sMapping);
	}
	
	public final function clearRemapping() {
		$this->m_oRemapping = null;
	}
	
	public final function getRemapping() {
		return $this->m_oRemapping;
	}
	
	public final function hasRemapping() {
		return $this->m_oRemapping !== null;
	}
	
	public final function setNewModel($sModel, array $aParams = array()) {
		return $this->m_oModel = $this->getWatena()->getContext()->loadModel($sModel, $aParams);
	}
	
	public final function clearNewModel() {
		$this->m_oModel = null;
	}
	
	public final function getNewModel() {
		return $this->m_oModel;
	}
	
	public final function hasNewModel() {
		return $this->m_oModel !== null;
	}
	
	public final function setNewView($sView, array $aParams = array()) {
		return $this->m_oView = $this->getWatena()->getContext()->loadView($sView, $aParams);
	}
	
	public final function clearNewView() {
		$this->m_oView = null;
	}
	
	public final function getNewView() {
		return $this->m_oView;
	}
	
	public final function hasNewView() {
		return $this->m_oView !== null;
	}
	
	public final function display($sMessage) {
		echo '<div style="background: #CCC; text-align:center; border:1px solid #000; padding:5px; font-weight:bold;">'.$sMessage.'</div>';
	}
	
	public final function displayf() {
		$aArgs = func_get_args();
		$sMessage = array_shift($aArgs);
		if($sMessage)
			$this->display(vprintf($sMessage, $aArgs));
	}
}

?>