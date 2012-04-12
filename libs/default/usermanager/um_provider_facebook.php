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
			
			if(isset($aData['email']))
				$oUser->addEmail($aData['email'], true);
			
			if((!$oUser->getGender() || $bForceOverwrite) && isset($aData['gender']))
				$oUser->setGender($aData['gender']);
			
			if((!$oUser->getBirthday() || $bForceOverwrite) && isset($aData['birthday']))
				$oUser->setBirthday($aData['birthday']);
			
			if((!$oUser->getFirstname() || $bForceOverwrite) && isset($aData['first_name']))
				$oUser->setFirstname($aData['first_name']);
			
			if((!$oUser->getLastname() || $bForceOverwrite) && isset($aData['last_name']))
				$oUser->setLastname($aData['last_name']);
			
			if((!$oUser->getTimezone() || $bForceOverwrite) && isset($aData['timezone']))
				$oUser->setTimezone($aData['timezone']);
			
			if((!$oUser->getLocale() || $bForceOverwrite) && isset($aData['locale']))
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
			if(($nId = UserManager::getUserIdByName($this->getConnectionName())) !== false && (!$oUser || $oUser->getId() != $nId))
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
			return isset($aData['username']) ? $aData['username'] : $aData['name'];
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
		return $this->getConnectionId() && (bool)$this->getConnectionData();
	}
	
	public function disconnect() {
		return !$this->isConnected();
	}
	
	public function connect() {
		return $this->isConnected();
	}
}

?>