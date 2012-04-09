<?php

class User extends DbObject {
	
	private $m_aConnections = null;
	private $m_aEmails = null;
	
	public function getName() {
		return $this->getDataValue('name');
	}
	
	public function getTimezone() {
		return $this->getDataValue('timezone');
	}
	
	public function getLocale() {
		return $this->getDataValue('locale');
	}
	
	public function setTimezone($mValue) {
		$this->setDataValue('timezone', $mValue);
	}
	
	public function setLocale($mValue) {
		$this->setDataValue('locale', $mValue);
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
		$oConnection = UserConnection::create($this, $oConnectionProvider);
		if($oConnection) {
			$this->m_aConnections []= $oConnection;
			return $oConnection;
		}
		else {
			return false;
		}
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
		$oEmail = UserEmail::create($this, $sEmail, $bVerified);
		if($oEmail) {
			$this->m_aEmails []= $oEmail;
			return $oEmail;
		}
		else {
			return false;
		}
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