<?php
require_plugin('Ajax');

class AjaxModel extends Model {
	
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
	
	public final function addException(Exception $e) {
		
	}
	
	public final function addError() {
		
	}
}

?>