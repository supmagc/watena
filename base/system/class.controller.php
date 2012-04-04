<?php

abstract class Controller extends CacheableData {
	
	abstract public function process(Model $oModel, View $oView);
	
	private $m_oRemapping;
	
	public final function redirect($sLocation) {
		header('location: ' . $sLocation, true);
	}
	
	public final function setRemapping($sMapping) {
		$this->m_oRemapping = new Mapping($sMapping);
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
	
	public final function display($sMessage) {
		echo '<div style="background: #CCC; text-align:center; border:1px solid #000; padding:5px; font-weight:bold;">'.$sMessage.'</div>';
	}
}

?>