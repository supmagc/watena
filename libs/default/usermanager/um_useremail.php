<?php

class UserEmail extends UserManagerVerifiable {
	
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	public function getEmail() {
		return $this->getDataValue('email');
	}
	
	public function getTimestamp() {
		return $this->getDataValue('timestamp');
	}
	
	public static function load($mData) {
		return DbObject::loadObject('UserEmail', UserManager::getDatabaseConnection()->getTable('user_email'), $mData);
	}
	
	public static function create(User $oUser, $sEmail, $bVerified = false) {
		return (!UserManager::getUserIdByEmail($sEmail) && UserManager::isValidEmail($sEmail)) ? DbObject::createObject('UserEmail', UserManager::getDatabaseConnection()->getTable('user_email'), array(
			'userId' => $oUser->getId(),
			'email' => $sEmail,
			'verified' => $bVerified ? 1 : 0
		)) : false;
	}
}

?>