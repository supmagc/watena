<?php

class JSFunction extends Object {
	
	private $m_sFunction;
	private $m_aParams;
	
	public function __construct($sFunction, array $aParams = array()) {
		$this->m_sFunction = $sFunction;
		$this->m_aParams = $aParams;
	}
	
	public final function getFunction() {
		return $this->m_sFunction;
	}
	
	public final function getParameters() {
		return $this->m_aParams;
	}

	public final function getFunction() {
		return 'function() {window['.$this->m_sFunction.'].apply(this, '.json_encode($this->m_aParams).');}';
	}
	
	public final function getCallback($sCallbackName, $bCloseStatement = true) {
		return "var $sCallbackName = " . $this->getFunction() . ';' . ($bCloseStatement ? ';' : '');
	}
	
	public final function callFunction($bCloseStatement = true) {
		return '(' . $this->getFunction() . ')()' . ($bCloseStatement ? ';' : '');
	}
}

?>