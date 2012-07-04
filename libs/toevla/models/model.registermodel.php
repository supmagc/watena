<?php

class RegisterModel extends Model {
	
	private $m_nType;
	private $m_sHash;
	
	public function getHash() {
		return $this->m_sHash;
	}
	
	public function getEmail() {
		return isset($_POST['email']) ? $_POST['email'] : false;
	}
	
	public function getPass() {
		return isset($_POST['pass']) ? $_POST['pass'] : false;
	}
	
	public function showInvalidEmail() {
		$this->m_nType = 0;
	}
	
	public function showUnverifiedEmail() {
		$this->m_nType =1;
	}
	
	public function showLogin() {
		$this->m_nType = 2;
	}
	
	public function showRegister() {
		$this->m_nType = 3;
	}
	
	public function showInvalidPassword() {
		$this->m_nType = 4;
	}
	
	public function showRegisterDone() {
		$this->m_nType = 5;
	}
	
	public function showDone($sHash) {
		$this->m_sHash = $sHash;
		$this->m_nType = 6;
	}
	
	public function isInvalidEmail() {
		return $this->m_nType == 0;
	}
	
	public function isUnverifiedEmail() {
		return $this->m_nType == 1;
	}
	
	public function isLogin() {
		return $this->m_nType == 2;
	}
	
	public function isRegister() {
		return $this->m_nType == 3;
	}
	
	public function isInvalidPassword() {
		return $this->m_nType == 4;
	}	
	
	public function isRegisterDone() {
		return $this->m_nType == 5;
	}
	
	public function isDone() {
		return $this->m_nType == 6;
	}
}

?>