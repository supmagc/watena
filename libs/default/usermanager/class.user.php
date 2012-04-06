<?php

class User {
	
	private $m_aData;
	private $m_aConnections = null;
	
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
			'data' => $oConnectionProvider->getConnectionData(),
			'tokens' => $oConnectionProvider->getConnectionTokens()
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
	
	public function addEmail($sEmail) {
		
	}
	
	public function removeEmail(UserEmail $oEmail) {
		
	}
}

?>