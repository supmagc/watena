<?php

class UserEmail {
	
	private $m_aData;

	public function __construct($mData) {
		if(is_array($mData)) {
			$this->m_aData = $mData;
		}
		else if(is_numeric($mData)) {
			$this->m_aData = UserManager::getDatabaseConnection()->select('user', (int)$mData)->fetch(PDO::FETCH_ASSOC);
		}
		else {
			throw new UserInvalidIdException();
		}
	}
	
	public function getId() {
		return $this->m_aData['ID'];
	}
	
	public function getEmail() {
		return $this->m_aData['email'];
	}
	
	public function getHash() {
		return $this->m_aData['hash'];
	}
	
	public function getTimestamp() {
		return $this->m_aData['timestamp'];
	}
	
	public function isVerified() {
		return (bool)$this->m_aData['verified'];
	}
}

?>