<?php

class IPCO_Processor {
	
	private $m_aComponents = array();
	
	public function componentPush($mComponent) {
		if($mComponent !== null) {
			array_push($this->m_aComponents, $mComponent);
			return true;
		}
		return false;
	}
	
	public function componentPop() {
		array_pop($this->m_aComponents);
	}
	
	protected function processMethod($sName, array $aParams, $mBase = null) {
		$bCompCheck = $this->componentPush($mBase);
		if($bCompCheck) $this->componentPop();
	}
	
	protected function processMember($sName, $mBase = null) {
		$bCompCheck = $this->componentPush($mBase);
		if($bCompCheck) $this->componentPop();
	}
	
	protected function processSlices(array $aSliced, $mBase = null) {
		$bCompCheck = $this->componentPush($mBase);
		if($bCompCheck) $this->componentPop();
	}
}

?>