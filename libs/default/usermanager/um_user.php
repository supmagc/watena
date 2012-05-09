<?php

class User extends DbObject {
	
	private $m_aConnections = null;
	private $m_aEmails = null;
	
	public function getName() {
		return $this->getDataValue('name');
	}

	public function getGender() {
		return $this->getDataValue('gender');
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
			$this->m_aEmails []= $oEmail;
		}
		else if($oEmail = $this->getEmail($sEmail)) {
			if(!$oEmail->getVerified() && $bVerified)
				$oEmail->setVerified(true);
		}
		return $this->getEmail($sEmail);
	}
	
	public function getEmail($sEmail) {
		$aEmails = $this->getEmails();
		return isset($aEmails[$sEmail]) ? $aEmails[$sEmail] : false;
	}
	
	public function removeEmail(UserEmail $oEmail) {
		if(isset($this->m_aEmails[$oEmail->getEmail()])) {
			$oEmail->delete();
			unset($this->m_aEmails[$oEmail->getEmail()]);
		}
	}
	
	public static function load($mData) {
		return DbObject::loadObject('User', UserManager::getDatabaseConnection()->getTable('user'), $mData);
	}
	
	public static function create($sName, $nType) {
		return DbObject::createObject('User', UserManager::getDatabaseConnection()->getTable('user'), array(
			'type' => $nType,
			'name' => $sName
		));	
	}
}

?>