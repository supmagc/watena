<?php
require_plugin('Socializer');

class ProviderFacebook extends UserConnectionProvider {
	
	private $m_aData = null;
	
	public function update(User $oUser, $bForceOverwrite = false) {
		$aData = $this->getConnectionData();
		if(is_array($aData)) {
			$oConnection = UserManager::getDatabaseConnection();
			$oStatement = $oConnection->select('user_email', $aData['email'], 'email');
			if($oStatement->rowCount() > 0) {
				if($oStatement->fetchObject()->userId != $oUser->getId())
					throw new UserConnectionException(UserConnectionException::DUPLICATE_EMAIL);
			}
			else {
				$oUser->addEmail($aData['email']);
			}
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
			$oConnection = UserManager::getDatabaseConnection();
			$oStatement = $oConnection->select('user_email', $aData['email'], 'email');
			if($oStatement->rowCount() > 0 && $oUser !== null && $oStatement->fetchObject()->userId != $oUser->getId())
				throw new UserConnectionException(UserConnectionException::DUPLICATE_EMAIL);
			$oStatement = $oConnection->select('user', $aData['username'], 'name');
			if($oStatement->rowCount() > 0 && $oUser !== null && $oStatement->fetchObject()->ID != $oUser->getId())
				throw new UserConnectionException(UserConnectionException::DUPLICATE_NAME);
			return true;
		}
		return false;
	}
	
	public function getConnectionId() {
		$nId = Socializer::facebook()->getUser();
		return $nId > 0 ? $nId : false;
	}
	
	public function getConnectionData() {
		if($this->getConnectionId()) {
			if($this->m_aData === null) {
				try {
					$this->m_aData = Socializer::facebook()->api('/me');
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
		return $this->getConnectionId() ? Socializer::facebook()->getAccessToken() : false;
	}
	
	public function getConnectUrl($sRedirect) {
		return Socializer::facebook()->getLoginUrl(array('scope' => 'email', 'redirect_uri' => $sRedirect));
	}
	
	public function getDisconnectUrl($sRedirect) {
		return Socializer::facebook()->getLogoutUrl(array('next' => $sRedirect));
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