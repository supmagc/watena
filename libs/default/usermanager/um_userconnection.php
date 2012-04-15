<?php

class UserConnection extends DbObject {
	
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	public function getProvider() {
		return $this->getDataValue('provider');
	}
	
	public function getConnectionId() {
		return (int)$this->getDataValue('connectionId');
	}
	
	public function getConnectionData() {
		return json_decode($this->getDataValue('connectionData'), true);
	}
	
	public function getConnectionTokens() {
		return json_decode($this->getDataValue('connectionTokens'), true);
	}
	
	public function getConnectionTimestamp() {
		return $this->getDataValue('timestamp');
	}
	
	public function setConnectionData($mValue) {
		$this->setDataValue('connectionData', json_encode($mValue));
	}
	
	public function setConnectionTokens($mValue) {
		$this->setDataValue('connectionTokens', json_encode($mValue));
	}
	
	public static function load($mData) {
		return DbObject::loadObject('UserConnection', UserManager::getDatabaseConnection()->getTable('user_connection'), $mData);
	}
	
	public static function create(User $oUser, UserConnectionProvider $oConnectionProvider) {
		return !UserManager::getUserIdByConnection($oConnectionProvider) ? DbObject::createObject('UserConnection', UserManager::getDatabaseConnection()->getTable('user_connection'), array(
			'userId' => $oUser->getId(),
			'provider' => $oConnectionProvider->getName(),
			'connectionId' => $oConnectionProvider->getConnectionId(),
			'connectionData' => json_encode($oConnectionProvider->getConnectionData()),
			'connectionTokens' => json_encode($oConnectionProvider->getConnectionTokens())
		)) : false;
	}
}

?>