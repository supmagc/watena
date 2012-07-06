<?php
require_model('HtmlModel');

class ConnectModel extends HtmlModel {
	
	private $m_sHash;
	private $m_nType = 0;
	private $m_sName;
	
	public function setName($sName) {
		$this->m_sName = $sName;
	}
	
	public function setHash($sHash) {
		$this->m_sHash = $sHash;
	}
	
	public function getHash() {
		return $this->m_sHash;
	}

	public function showDuplicateName() {
		$this->m_nType = 7;
	}
	
	public function showDuplicateLogin() {
		$this->m_nType = 6;		
	}
	
	public function showDuplicateEmail() {
		$this->m_nType = 5;
	}
	
	public function showInvalidEmail() {
		$this->m_nType = 4;
	}
	
	public function showInvalidName() {
		$this->m_nType = 3;
	}
	
	public function showUnverifiedEmail() {
		$this->m_nType = 2;
	}
	
	public function showFailed() {
		$this->m_nType = 1;
	}

	public function showSucces() {
		$this->m_nType = 0;
	}
	
	public function getSaveName() {
		return htmlentities($this->m_sName);
	}
	
	public function isSucces() {
		return $this->m_nType == 0;
	}
	
	public function isFailed() {
		return $this->m_nType == 1;
	}
	
	public function isUnverifiedEmail() {
		return $this->m_nType == 2;
	}
	
	public function isInvalidName() {
		return $this->m_nType == 3;
	}
	
	public function isInvalidEmail() {
		return $this->m_nType == 4;
	}
	
	public function isDuplicateEmail() {
		return $this->m_nType == 5;
	}
	
	public function isDuplicateLogin() {
		return $this->m_nType == 6;
	}
	
	public function isDuplicateName() {
		return $this->m_nType == 7;
	}
}

?>