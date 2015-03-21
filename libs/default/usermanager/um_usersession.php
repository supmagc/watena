<?php

class UserSession extends DbMultiObject {
	
	private $m_oUser = false;
	private $m_oTime = false;
	private $m_oActivity = false;

	public function getKeyForContainer(Container $oContainer) {
		return $this->getDataValue('token');
	}
	
	public function onRemovedFromContainer(Container $oContainer) {
		$this->delete();
	}
	
	public function getUserId() {
		return $this->getDataValue('userId');
	}
	
	public function getUser() {
		if(false === $this->m_oUser) {
			$this->m_oUuser = User::load($this->getUserId());
		}
		return $this->m_oUser;
	}
	
	public function getToken() {
		return $this->getDataValue('token');
	}

	public function getTime() {
		if(false === $this->m_oTime) {
			$this->m_oTime = new Time($this->getDataValue('timestamp'));
		}
		return $this->m_oTime;
	}
	
	public function getActivity() {
		if(false === $this->m_oActivity) {
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
		return self::loadObject(self::getDbTable(), array($oUser->getId(), $sToken));
	}
	
	public static function create(User $oUser, $sIp, $sUserAgent) {
		$sToken = md5($oUser->getId() . mt_rand() . microtime());
		$sToken = substr($sToken, mt_rand(0, 16), 16);
		$oInstance = self::createObject(self::getDbTable(), array(
			'userId' => $oUser->getId(),
			'token' => $sToken,
			'ip' => $sIp,
			'useragent' => $sUserAgent
		));
		$oUser->getContainerSessions()->addItem($oInstance);
		return $oInstance;
	}
}
