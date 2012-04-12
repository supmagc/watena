<?php

class CallbackModel extends Model {
	
	private $m_sHash;
	private $m_nType = 0;
	private $m_sError;
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

	public function showDuplicateName($sError) {
		$this->m_nType = 4;
		$this->m_sError = $sError;
	}
	
	public function showDuplicateConnection() {
		$this->m_nType = 3;		
	}
	
	public function showDuplicateEmail() {
		$this->m_nType = 2;
	}
	
	public function showFailed() {
		$this->m_nType = 1;
	}

	public function showSucces() {
		$this->m_nType = 0;
	}
	
	public function getError() {
		return $this->m_sError;
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
	
	public function isDuplicateEmail() {
		return $this->m_nType == 2;
	}
	
	public function isDuplicateConnection() {
		return $this->m_nType == 3;
	}
	
	public function isDuplicateName() {
		return $this->m_nType == 4;
	}
}

?>