<?php

class ProviderTwitter extends UserConnectionProvider {
	
	private $m_aData = null;
	private $m_oTwitter;
	
	public function __construct() {
		$this->m_oTwitter = watena()->getContext()->getPlugin('Socializer')->getTwitter();
		if(!$this->m_oTwitter) {
			throw new UserConnectionProviderInitializeFailed();
		}
	}
	
	public function update(User $oUser, $bForceOverwrite = false) {
		$aData = $this->getConnectionData();
		if(is_array($aData)) {
			if((!$oUser->getFirstname() || $bForceOverwrite) && isset($aData['name']))
				$oUser->setFirstname(Encoding::substring($aData['name'], 0, Encoding::indexOf($aData['name'], ' ')));
			
			if((!$oUser->getLastname() || $bForceOverwrite) && isset($aData['name']))
				$oUser->setLastname(Encoding::substring($aData['name'], Encoding::indexOf($aData['name'], ' ') + 1));
					
			if((!$oUser->getTimezone() || $bForceOverwrite) && isset($aData['time_zone']))
				$oUser->setTimezone($aData['time_zone']);
			
			if((!$oUser->getLocale() || $bForceOverwrite) && isset($aData['lang']))
				$oUser->setLocale($aData['lang']);
			return true;
		}
		return false;
	}
	
	public function getConnectionId() {
		return $this->isConnected() ? $this->m_oTwitter->getUserId() : false;
	}
	
	public function getConnectionName() {
		return $this->isConnected() ? $this->m_oTwitter->getUserName() : false;
	}
	
	public function getConnectionEmail() {
		return false;
	}
	
	public function getConnectionData() {
		if($this->m_oTwitter->getUserId()) {
			if($this->m_aData === null) {
				$this->m_aData = $this->m_oTwitter->api('/account/verify_credentials.json', 'GET', array('skip_status' => 1));
			}
			return $this->m_aData;
		}
		return false;
	}
	
	public function getConnectionTokens() {
		return $this->m_oTwitter->getAccessToken();
	}
	
	public function getConnectUrl($sRedirect, $sScope = null) {
		return $this->m_oTwitter->getLoginUrl($sRedirect);
	}
	
	public function getDisconnectUrl($sRedirect) {
		return false;
	}
	
	public function isConnected() {
		return $this->m_oTwitter->getUserId() && is_array($this->getConnectionData());
	}
	
	public function disconnect() {
		return $this->m_oTwitter->logout();
	}
	
	public function connect() {
		return $this->m_oTwitter->login();
	}
}

?>