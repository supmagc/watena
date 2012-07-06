<?php

class RegisterModel extends Model {
	
	private $m_nType;
	private $m_sHash;
	private $m_sHeader;
	private $m_sText;
	private $m_bEmail;
	private $m_bPass;
	private $m_bSucces;
	private $m_bError;
	
	public function getHash() {
		return $this->m_sHash;
	}
	
	public function getEmail() {
		return isset($_POST['email']) ? $_POST['email'] : false;
	}
	
	public function getPass() {
		return isset($_POST['pass']) ? $_POST['pass'] : false;
	}
	
	public function getHeader() {
		return $this->m_sHeader;
	}
	
	public function getText() {
		return $this->m_sText;
	}
	
	public function getShowEmail() {
		return $this->m_bEmail;
	}
	
	public function getShowPass() {
		return $this->m_bPass;
	}
	
	public function getShowSucces() {
		return $this->m_bSucces;
	}
	
	public function getShowError() {
		return $this->m_bError;
	}
	
	public function showDone($sTitle, $sText, $sHash) {
		$this->m_sHeader = $sTitle;
		$this->m_sText = $sText;
		$this->m_sHash = $sHash;
		$this->m_bSucces = true;
	}
	
	public function showEmail($sTitle, $sText, $bError = false) {
		$this->m_sHeader = $sTitle;
		$this->m_sText = $sText;
		$this->m_bEmail = true;
		$this->m_bError = $bError;
	}
	
	public function showPass($sTitle, $sText, $bError = false) {
		$this->m_sHeader = $sTitle;
		$this->m_sText = $sText;
		$this->m_bPass = true;
		$this->m_bError = $bError;
	}
	
	public function showError($sTitle, $sText) {
		$this->m_sHeader = $sTitle;
		$this->m_sText = $sText;
		$this->m_bError = true;
	}
	
	public function showSucces($sTitle, $sText) {
		$this->m_sHeader = $sTitle;
		$this->m_sText = $sText;
		$this->m_bSucces = true;
	}
	
	public function showMessage($sTitle, $sText) {
		$this->m_sHeader = $sTitle;
		$this->m_sText = $sText;
	}
}

?>