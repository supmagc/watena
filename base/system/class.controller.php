<?php

abstract class Controller extends CacheableData {
	
	abstract public function process(Model $oModel = null, View $oView = null);
	
	private $m_oRemapping;
	
	public final function redirect($sLocation) {
		if(Encoding::substring($sLocation, 0, 4) !== 'http') $sLocation = $this->getWatena()->getMapping()->getRoot() . $sLocation;
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
	
	public final function displayf($sMessage) {
		$aArgs = func_get_args();
		$sMessage = array_shift($aArgs);
		if($sMessage)
			$this->display(vprintf($sMessage, $aArgs));
	}
}

?>