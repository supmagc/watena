<?php

class ProviderFacebook extends UserConnectionProvider {
	
	private $m_aData = null;
	private $m_oFacebook;
	
	public function __construct() {
		$this->m_oFacebook = watena()->getContext()->getPlugin('Socializer')->getFacebook();
		if(!$this->m_oFacebook)
			throw new UserConnectionProviderFailed();
	}
	
	public function update(User $oUser, $bForceOverwrite = false) {
		$aData = $this->getConnectionData();
		if(is_array($aData)) {
			if(!UserManager::getUserIdByEmail($aData['email']))
				$oUser->addEmail($aData['email'], true);
			if(!$oUser->getTimezone() || $bForceOverwrite)
				$oUser->setTimezone($aData['timezone']);
			if(!$oUser->getLocale() || $bForceOverwrite)
				$oUser->setLocale($aData['locale']);
			return true;
		}
		return false;
	}
	
	public function canBeConnectedTo(User $oUser = null) {
		if($this->isConnected()) {
			$aData = $this->getConnectionData();
			if(($nId = UserManager::getUserIdByEmail($aData['email'])) !== false && (!$oUser || $oUser->getId() != $nId))
				throw new UserDuplicateEmailException();
			if(($nId = UserManager::getUserIdByName($aData['username'])) !== false && (!$oUser || $oUser->getId() != $nId))
				throw new UserDuplicateNameException();
			if(($nId = UserManager::getUserIdByConnection($this)) !== false && (!$oUser || $oUser->getId() != $nId))
				throw new UserDuplicateConnectionException();
			return true;
		}
		return false;
	}
	
	public function getConnectionId() {
		$nId = $this->m_oFacebook->getUser();
		return $nId > 0 ? $nId : false;
	}
	
	public function getConnectionName() {
		if($this->isConnected()) {
			$aData = $this->getConnectionData();
			return $aData['username'];
		}
		return false;
	}
	
	public function getConnectionData() {
		if($this->getConnectionId()) {
			if($this->m_aData === null) {
				try {
					$this->m_aData = $this->m_oFacebook->api('/me');
				}
				catch(FacebookApiException $e) {
					$this->m_aData = false;
				}
			}
			return $this->m_aData;
		}
		return false;
	}
	
	public function getConnectionTokens() {
		return $this->getConnectionId() ? $this->m_oFacebook->getAccessToken() : false;
	}
	
	public function getConnectUrl($sRedirect) {
		return $this->m_oFacebook->getLoginUrl(array('scope' => 'email', 'redirect_uri' => '' . $sRedirect));
	}
	
	public function getDisconnectUrl($sRedirect) {
		return $this->m_oFacebook->getLogoutUrl(array('next' => $sRedirect));
	}
	
	public function isConnected() {
		return $this->getConnectionId() && $this->getConnectionData() !== false;
	}
	
	public function disconnect() {
		return !$this->isConnected();
	}
	
	public function connect() {
		return $this->isConnected();
	}
}

?>