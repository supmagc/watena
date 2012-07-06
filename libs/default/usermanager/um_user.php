<?php

class User extends UserVerifiable {
	
	private $m_aConnections = null;
	private $m_aEmails = null;
	
	public function hasPassword() {
		return (bool)$this->getDataValue('password');
	}

	public function getGender() {
		return $this->getDataValue('gender');
	}
	
	public function getName() {
		return $this->getDataValue('name');
	}
	
	public function getBirthday() {
		return $this->getDataValue('birthday');
	}
	
	public function getFirstname() {
		return $this->getDataValue('firstname');
	}
	
	public function getLastname() {
		return $this->getDataValue('lastname');		
	}
	
	public function getTimezone() {
		return $this->getDataValue('timezone');
	}
	
	public function getLocale() {
		return $this->getDataValue('locale');
	}
	
	public function getHash() {
		return $this->getDataValue('hash');
	}
	
	public function isTla() {
		return (bool)$this->getDataValue('tla', false);
	}
	
	public function setGender($mValue) {
		$mValue = Encoding::toLower($mValue);
		if($mValue === 'm') $mValue = 'male';
		if($mValue === 'f') $mValue = 'female';
		if(in_array($mValue, array('male', 'female'))) {
			$this->setDataValue('gender', $mValue);
			return true;
		}
		return false;
	}
	
	public function setName($mValue) {
		$nUserId = UserManager::getUserIdByName($mValue);
		if(UserManager::isValidName($mValue) && (!$nUserId || $nUserId == $this->getId())) {
			$this->setDataValue('name', $mValue);
			return true;
		}
		return false;
	}
	
	public function setBirthday($mValue) {
		if(Encoding::regMatch('[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}', $sData)) {
			$this->setDataValue('birthday', $mValue);
			return true;
		}
		return false;
	}
	
	public function setFirstname($mValue) {
		$this->setDataValue('firstname', $mValue);
		return true;
	}
		
	public function setLastname($mValue) {
		$this->setDataValue('lastname', $mValue);
		return true;
	}
		
	public function setTimezone($mValue) {
		$this->setDataValue('timezone', Time::formatTimezone($mValue));
		return true;
	}
	
	public function setLocale($mValue) {
		$this->setDataValue('locale', $mValue);
		return true;
	}

	public function setPassword($mValue) {
		if(UserManager::isValidPassword($mValue)) {
			$this->setDataValue('password', $this->encodePassword($mValue));
			return true;
		}
		return false;
	}
	
	public function verifyPassword($mValue) {
		return $this->getDataValue('password') === $this->encodePassword($mValue);
	}
	
	public function getConnections() {
		if($this->m_aConnections === null) {
			$this->m_aConnections = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_connection', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$oConnection = UserConnection::load($aData);
				$this->m_aConnections[$oConnection->getProvider()] = $oConnection;
			}
		}
		return $this->m_aConnections;
	}
	
	public function addConnection(UserConnectionProvider $oConnectionProvider) {
		if($oConnection = UserConnection::create($this, $oConnectionProvider)) {
			$this->m_aConnections[$oConnection->getProvider()] = $oConnection;
		}
		return $this->getConnection($oConnectionProvider);
	}
	
	public function getConnection(UserConnectionProvider $oConnectionProvider) {
		$aConnections = $this->getConnections();
		return isset($aConnections[$oConnectionProvider->getName()]) ? $aConnections[$oConnectionProvider->getName()] : false;
	}
	
	public function getConnectionFacebook() {
		$oConnection = UserManager::getProviderFacebook();
		return $oConnection ? $this->getConnection($oConnection) : false;
	}
	
	public function getConnectionTwitter() {
		$oConnection = UserManager::getProviderTwitter();
		return $oConnection ? $this->getConnection($oConnection) : false;
	}
	
	public function removeConnection(UserConnection $oConnection) {
		if(isset($this->m_aConnections[$oConnection->getProvider()])) {
			$oConnection->delete();
			unset($this->m_aConnections[$oConnection->getProvider()]);
		}
	}
	
	public function getEmails() {
		if($this->m_aEmails === null) {
			$this->m_aEmails = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_email', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$oEmail = UserEmail::load($aData);
				$this->m_aEmails[$oEmail->getEmail()] = $oEmail;
			}
		}
		return $this->m_aEmails;
	}
	
	public function addEmail($sEmail, $bVerified = false) {
		if($oEmail = UserEmail::create($this, $sEmail, $bVerified)) {
			$this->m_aEmails[Encoding::toLower($sEmail)] = $oEmail;
		}
		return $this->getEmail($sEmail);
	}
	
	public function getEmail($sEmail = null) {
		$aEmails = $this->getEmails();
		if($sEmail)
			return isset($aEmails[Encoding::toLower($sEmail)]) ? $aEmails[Encoding::toLower($sEmail)] : false;
		else 
			return count($aEmails) > 0 ? array_first($aEmails) : false;
	}
	
	public function removeEmail(UserEmail $oEmail) {
		if(isset($this->m_aEmails[$oEmail->getEmail()])) {
			$oEmail->delete();
			unset($this->m_aEmails[$oEmail->getEmail()]);
		}
	}
	
	public function encodePassword($mValue) {
		$sHash = Encoding::substring($this->getHash(), ($this->getId() / 3) % 16, ($this->getId() / 2) % 16);
		$sData = sprintf('%s.%s.%s', $this->getId(), $mValue, $sHash);
		return md5($sData);
	}
	
	public static function load($mData) {
		return DbObject::loadObject('User', UserManager::getDatabaseConnection()->getTable('user'), $mData);
	}
	
	public static function create($sName) {
		return DbObject::createObject('User', UserManager::getDatabaseConnection()->getTable('user'), array(
			'name' => $sName,
			'hash' => md5(mt_rand() . $sName . microtime())
		));	
	}
}

?>