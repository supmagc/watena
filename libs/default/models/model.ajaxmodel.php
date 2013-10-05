<?php

class AjaxModel extends Model {
	
	private $m_aLines = array();
	
	public function addJavascript($sCode) {
		$this->m_aLines []= $sCode;
	}
}

?>