<?php

class UserLogin extends DbMultiObject {
	
	public function loadAll() {
		
	}
	
	public function create(User $oUser) {
		return DbMultiObject::createObject('UserLogin', UserManager::getDatabaseConnection()->getMultiTable('user_login', array('userId', 'timestamp')), array(
			'userId' => $oUser->getId(),
			'timestamp' => Time::getSystemTimestamp()
		));
	}
}

?>