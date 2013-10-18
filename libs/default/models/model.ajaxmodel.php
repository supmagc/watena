<?php

abstract class AjaxModel extends Model {

	private $m_sCallback;
	private $m_aArguments = array();
	private $m_aValues = array();
	
	public function init() {
		if(isset($_POST['callback'])) {
			$this->m_sCallback = $_POST['callback'];
		}
		else if(isset($_GET['callback'])) {
			$this->m_sCallback = $_GET['callback'];
		}
	
		if(isset($_POST['args'])) {
			$this->m_aArguments = json_decode($_POST['args'], true);
		}
		else if(isset($_GET['args'])) {
			$this->m_aArguments = json_decode($_GET['args'], true);
		}
	
		if(isset($_POST['values'])) {
			$this->m_aValues = json_decode($_POST['values'], true);
		}
		else if(isset($_GET['values'])) {
			$this->m_aValues = json_decode($_GET['values'], true);
		}
	}
	
	public function getCallback() {
		return $this->m_sCallback;
	}
	
	public function getArguments() {
		return $this->m_aArguments;
	}

	public function getArgument($nIndex = 0) {
		return array_value($this->m_aArguments, $nIndex, null);
	}
	
	public function getValues() {
		return $this->m_aValues;
	}
	
	public function getValue($sName) {
		return array_value($this->m_aValues, $sName, null);
	}
	
	public function process() {
		if(!empty($this->m_sCallback)) {
			if(method_exists($oContext, $this->getCallback())) {
				return call_user_func_array(array($this, $this->getCallback()), $this->getArguments());
			}
		}
		return false;
	}
	
	
	
	
	/*
	private $m_oServer;
	private $m_aErrors;
	
	public abstract function generateAjax();
	
	public function init() {
		parent::init();
		$this->m_oServer = new AJAX_Server($this);
		foreach($this->m_oServer->getValues() as $sName => $mValue) {
			$this->$sName = $mValue;
		}
	}
	
	public final function getAjax() {
		return $this->m_oServer;
	}
	
	public final function alert($sMessage) {
		echo "alert(decodeURIComponent('".rawurlencode($sMessage)."'));\n";
	}
	
	public final function hasErrors() {
		return count($this->m_aErrors) > 0;
	}
	
	public final function getErrors() {
		return $this->m_aErrors;
	}
	
	public final function addException(Exception $oException) {
		$this->m_aErrors [] = sprintf("%s %s (line: %d)", $oException->getMessage(), $oException->getFile(), $oException->getLine());
	}
	
	public final function addError($sMessage) {
		$this->m_aErrors [] = $sMessage;
	}
	*/
}

?>