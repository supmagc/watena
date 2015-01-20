<?php

class UserSession extends DbMultiObject {
	
	public static function getDbTable() {
		return UserManager::getDatabaseConnection()->getMultiTable('user_session', array('userId', 'token'));
	}
	
	public static function load(User $oUser, $sToken) {
		return self::loadObject(self::getTable(), array($oUser->getId(), $sToken));
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

