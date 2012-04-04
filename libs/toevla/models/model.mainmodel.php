<?php
require_model('HtmlModel');
require_plugin('Socializer');

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

	public function getTwitterLoginUrl() {
		return Socializer::twitter()->getLoginUrl();
	}
}

?>