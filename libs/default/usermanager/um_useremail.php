<?php

class UserEmail extends DbObject {
	
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	public function getEmail() {
		return $this->getDataValue('email');
	}
	
	public function getHash() {
		return $this->getDataValue('hash');
	}
	
	public function getTimestamp() {
		return $this->getDataValue('timestamp');
	}
	
	public function getVerified() {
		return (bool)$this->getDataValue('verified');
	}
	
	public function getVerifier() {
		return $this->getDataValue('verifier', false);
	}
	
	public function setVerified($mValue) {
		$this->setDataValue('verified', $mValue ? 1 : 0);
		return true;
	}
	
	public function resetVerifier() {
		
	} 
	
	public static function load($mData) {
		return DbObject::loadObject('UserEmail', UserManager::getDatabaseConnection()->getTable('user_email'), $mData);
	}
	
	public static function create(User $oUser, $sEmail) {
		return (!UserManager::getUserIdByEmail($sEmail) && UserManager::isValidEmail($sEmail)) ? DbObject::createObject('UserEmail', UserManager::getDatabaseConnection()->getTable('user_email'), array(
			'userId' => $oUser->getId(),
			'email' => $sEmail,
			'verifier' => md5($oUser->getId() . mt_rand() . $sEmail . microtime(true))
		)) : false;
	}
}

?>