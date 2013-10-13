<?php
require_plugin('Ajax');

class AjaxModel extends Model {
	
	private $m_oServer;
	
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
}

?>