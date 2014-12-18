<?php

class UserSession extends DbMultiObject {
	
	public function load($mData) {
		$oTable = UserManager::getDatabaseConnection()->getMultiTable('user_session', array('ID', 'token'), 'OR');
		return DbObject::loadObject($sClass, $oTable, $mData)
	}
	
	public function create(User $oUser) {
		return DbMultiObject::createObject('UserSession', UserManager::getDatabaseConnection()->getMultiTable('user_session', array('userId', 'timestamp')), array(
			'userId' => $oUser->getId(),
			'timestamp' => Time::getSystemTimestamp()
		));
	}
}

