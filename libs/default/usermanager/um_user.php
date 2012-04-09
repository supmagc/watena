<?php

class User {
	
	private $m_aData;
	private $m_aConnections = null;
	private $m_aEmails = null;
	
	public function __construct($mData) {
		if(is_array($mData)) {
			$this->m_aData = $mData;
		}
		else if(is_numeric($mData)) {
			$this->m_aData = UserManager::getDatabaseConnection()->select('user', (int)$mData)->fetch(PDO::FETCH_ASSOC);
		}
		else {
			throw new UserInvalidIdException();
		}
	}
	
	public function getId() {
		return $this->m_aData['ID'];
	}
	
	public function getName() {
		return $this->m_aData['name'];
	}
	
	public function getTimezone() {
		return $this->m_aData['timezone'];
	}
	
	public function getLocale() {
		return $this->m_aData['locale'];
	}
	
	public function getConnections() {
		if($this->m_aConnections === null) {
			$this->m_aConnections = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_connection', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$this->m_aConnections []= new UserConnection($aData);
			}
		}
		return $this->m_aConnections;
	}
	
	public function addConnection(UserConnectionProvider $oConnectionProvider) {
		$nId = UserManager::getDatabaseConnection()->insert('user_connection', array(
			'userId' => $this->getId(),
			'provider' => get_class($oConnectionProvider),
			'connectionId' => $oConnectionProvider->getConnectionId(),
			'data' => json_encode($oConnectionProvider->getConnectionData()),
			'tokens' => json_encode($oConnectionProvider->getConnectionTokens())
		));
		$oConnection = new UserConnection($this, $nId);
		$this->m_aConnections []= $oConnection;
		return $oConnection;
	}
	
	public function removeConnection(UserConnection $oConnection) {
		$nIndex = array_search($oConnection, $this->getConnections());
		if($nIndex !== false) {
			UserManager::getDatabaseConnection()->delete('user_connection', $oConnection->getId());
			unset($this->m_aConnections[$nIndex]);
		}
	}
	
	public function getEmails() {
		if($this->m_aEmails === null) {
			$this->m_aEmails = array();
			$oStatement = UserManager::getDatabaseConnection()->select('user_email', $this->getId(), 'userId');
			foreach($oStatement as $aData) {
				$this->m_aEmails[$aData['email']] = new UserEmail($aData);
			}
		}
		return $this->m_aEmails;
	}
	
	public function addEmail($sEmail, $bVerified = false) {
		if(!UserManager::isEmailAvailable($sEmail) && !$this->getEmail($sEmail) === null) {
			throw new UserDuplicateEmailException();
		}
		$nId = UserManager::getDatabaseConnection()->insert('user_email', array(
			'userId' => $this->getId(),
			'email' => $sEmail,
			'verified' => $bVerified ? 1 : 0,
			'hash' => md5($this->getId() . $sEmail . microtime(true) . mt_rand())
		));
		$oEmail = new UserEmail($nId);
		$this->m_aEmails[$sEmail] = $oEmail;
		return $oEmail;
	}
	
	public function getEmail($sEmail) {
		$aEmails = $this->getEmails();
		return isset($aEmails[$sEmail]) ? $aEmails[$sEmail] : null;
	}
	
	public function removeEmail(UserEmail $oEmail) {
		$nIndex = array_search($oEmail, $this->getEmails());
		if($nIndex !== false) {
			UserManager::getDatabaseConnection()->delete('user_email', $oEmail->getId());
			unset($this->m_aEmails[$nIndex]);
		}
	}
}

?>