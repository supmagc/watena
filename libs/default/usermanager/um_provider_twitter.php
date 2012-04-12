<?php

class ProviderTwitter extends UserConnectionProvider {
	
	private $m_aData = null;
	private $m_oTwitter;
	
	public function __construct() {
		$this->m_oTwitter = watena()->getContext()->getPlugin('Socializer')->getTwitter();
		if(!$this->m_oTwitter) {
			throw new UserConnectionProviderFailed();
		}
	}
	
	public function update(User $oUser, $bForceOverwrite = false) {
		$aData = $this->getConnectionData();
		if(is_array($aData)) {
			return true;
		}
		return false;
	}
		
	public function canBeConnectedTo(User $oUser = null) {
		if($this->isConnected()) {
			if(($nId = UserManager::getUserIdByName($this->getConnectionName())) !== false && (!$oUser || $oUser->getId() != $nId))
				throw new UserDuplicateNameException();
			if(($nId = UserManager::getUserIdByConnection($this)) !== false && (!$oUser || $oUser->getId() != $nId))
				throw new UserDuplicateConnectionException();
			return true;
		}
		return false;
	}
	
	public function getConnectionId() {
		return $this->m_oTwitter->getUserId();
	}
	
	public function getConnectionName() {
		return $this->m_oTwitter->getUserName();
	}
	
	public function getConnectionData() {
		if($this->m_aData === null) {
			$this->m_aData = $this->m_oTwitter->api('/account/verify_credentials.json', 'GET', array('skip_status' => 1));
		}
		return $this->m_aData;
	}
	
	public function getConnectionTokens() {
		return $this->m_oTwitter->getAccessToken();
	}
	
	public function getConnectUrl($sRedirect) {
		return $this->m_oTwitter->getLoginUrl($sRedirect);
	}
	
	public function getDisconnectUrl($sRedirect) {
		return false;
	}
	
	public function isConnected() {
		return $this->m_oTwitter->isLoggedIn() && (bool)$this->getConnectionData();
	}
	
	public function disconnect() {
		return $this->m_oTwitter->logout();
	}
	
	public function connect() {
		return $this->m_oTwitter->login();
	}
}

?>