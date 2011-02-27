<?php

class IPCO_Processor {
	
	private $m_aComponents = array();
	
	protected function processMethod($sName, array $aParams, $mBase = null) {
		$bCompCheck = $this->_tryAddBaseComponent($mBase);
		$this->_checkComponentRemoval($bCompCheck);
	}
	
	protected function processMember($sName, $mBase = null) {
		$bCompCheck = $this->_tryAddBaseComponent($mBase);
		$this->_checkComponentRemoval($bCompCheck);
	}
	
	protected function processSlices(array $aSliced, $mBase = null) {
		$bCompCheck = $this->_tryAddBaseComponent($mBase);
		$this->_checkComponentRemoval($bCompCheck);
	}
	
	private function _tryAddBaseComponent($mComponent) {
		if($mComponent !== null) {
			array_push($this->m_aComponents, $mComponent);
			return true;
		}
		return false;
	}
	
	private function _checkComponentRemoval($bEnabled) {
		if($bEnabled) {
			array_pop($this->m_aComponents);
		}
	}
}

?>