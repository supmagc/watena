<?php

class UserSession extends DbMultiObject {
	
	public static function getDbTable() {
		return UserManager::getDatabaseConnection()->getMultiTable('user_session', array('userId', 'token'));
	}
	
	public static function loadBySessionKey($mData) {
		$oTable = UserManager::getDatabaseConnection()->getMultiTable('user_session', array('ID', 'token'), 'OR');
		return DbObject::loadObject($sClass, $oTable, $mData)
	}
	
	public static function create(User $oUser) {
		return self::createObject(self::getDbTable(), array(
			'userId' => $oUser->getId(),
			'token' => $sToken,
			'ip' => Request::ip(),
			'useragent' => Request::useragent()
		));
	}
}

