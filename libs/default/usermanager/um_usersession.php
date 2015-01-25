<?php

class UserSession extends DbMultiObject {
	
	private $m_oUser = null;
	private $m_oCreated = null;
	private $m_oActivity = null;
	
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	public function getUser() {
		if(!$this->m_oUser) {
			$this->m_oUuser = User::load($this->getUserId());
		}
		return $this->m_oUser;
	}
	
	public function getToken() {
		return $this->getDataValue('token');
	}
	
	public function getActivity() {
		if(!$this->m_oActivity) {
			$this->m_oActivity = new Time($this->getDataValue('activity'));
		}
		return $this->m_oActivity;
	}
	
	public function setActivity(Time $oTime) {
		if($this->setDataValue('activity', $oTime->formatSqlTimestamp())) {
			$this->m_oActivity = $oTime;
		}
	}
	
	public function getIp() {
		return $this->getDataValue('ip');
	}
	
	public function setIp($sIp) {
		$this->setDataValue('ip', $sIp);
	}

	public function getUseragent() {
		return $this->getDataValue('useragent');
	}
	
	public function setUseragent($sUseragent) {
		$this->setDataValue('useragent', $sUseragent);
	}
	
	public static function getDbTable() {
		return UserManager::getDatabaseConnection()->getMultiTable('user_session', array('userId', 'token'));
	}
	
	public static function load(User $oUser, $sToken) {
		return self::loadObject(self::getTable(), array($oUser->getId(), $sToken));
	}
	
	public static function create(User $oUser, $sIp, $sUserAgent) {
		$sToken = md5($oUser->getId() . mt_rand() . microtime());
		$sToken = substr($sToken, mt_rand(0, 16), 16);
		return self::createObject(self::getDbTable(), array(
			'userId' => $oUser->getId(),
			'token' => $sToken,
			'ip' => $sIp,
			'useragent' => $sUserAgent
		));
	}
}

