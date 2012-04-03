<?php
require_model('HtmlModel');

class MainModel extends HtmlModel {
	
	private $m_sHash = null;
	
	public function setHash($sHash) {
		$this->m_sHash = $sHash;
	}
	
	public function hasHash() {
		return $this->m_sHash !== null;
	}
	
	public function getHash() {
		return $this->m_sHash;
	}
}

?>