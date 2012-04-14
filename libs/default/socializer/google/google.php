<?php

class Google {
	
	private $m_aConfig;
	private $m_oConsumer;
	private $m_oProvider;
	
	private $m_oOAuthClient;
	private $m_nLoginUrls = 0;
	
	public function __construct(array $aConfig) {
		$this->m_aConfig = $aConfig;
				
		$this->m_oConsumer = new OAuthConsumer($aConfig['consumer_key'], $aConfig['consumer_secret']);
		
		$this->m_oProvider = new OAuthProvider('Google');
		$this->m_oProvider->setSignatureMethod('HMAC-SHA1');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_REQUEST_TOKEN, 'https://api.twitter.com/oauth/request_token');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_REQUEST_TOKEN, 'POST');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_AUTHENTICATE, 'https://api.twitter.com/oauth/authenticate');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_AUTHENTICATE, 'GET');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_ACCESS_TOKEN, 'https://api.twitter.com/oauth/access_token');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_ACCESS_TOKEN, 'POST');		
		$this->m_oProvider->setUrl(OAuth::PROVIDER_API, 'https://api.twitter.com/1');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_API, 'GET');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_DEAUTHENTICATE, 'https://api.twitter.com/1/account/end_session.json');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_DEAUTHENTICATE, 'POST');
		
		list($oRequestToken, $oAccessToken) = $this->loadFromSession();
		$this->m_oOAuthClient = new OAuthClient($this->m_oProvider, $this->m_oConsumer, $oRequestToken, $oAccessToken);
	}
	
	public function __sleep() {
		return array('m_aConfig', 'm_oConsumer', 'm_oProvider');
	}
	
	public function __wakeup() {
		list($oRequestToken, $oAccessToken) = $this->loadFromSession();
		$this->m_oOAuthClient = new OAuthClient($this->m_oProvider, $this->m_oConsumer, $oRequestToken, $oAccessToken);
	}
	
	public function __destruct() {
		$_SESSION[$this->getSessionName('accesstoken')] = serialize($this->m_oOAuthClient->getAccessToken());
		$_SESSION[$this->getSessionName('requesttoken')] = serialize($this->m_oOAuthClient->getRequestToken());
	}
	
	public function getLoginUrl($sRedirect, $sScope) {
		return $this->m_oOAuthClient->getAuthenticationUrl(array('oauth_callback' => $sRedirect, 'scope' => $sScope), $this->m_nLoginUrls++ == 0);
	}
	
	public function login() {
		if($this->m_oOAuthClient->authenticate()) {
			return true;
		}
		return false;
	}
	
	public function logout() {
		$this->m_oOAuthClient->deauthenticate();
		return true;
	}
	
	public function api($sUrl = null, $sMethod = null, array $aParams = array()) {
		return json_decode($this->m_oOAuthClient->api($sUrl, $sMethod, $aParams), true);
	}
	
	public function apiWithToken(OAuthToken $oAccessToken, $sUrl = null, $sMethod = null, array $aParams = array()) {
		$oOAuthClient = new OAuthClient($this->m_oProvider, $this->m_oConsumer, null, $oAccessToken);
		return json_decode($oOAuthClient->api($sUrl, $sMethod, $aParams), true);
	}
	
	public function getAccessToken() {
		return $this->m_oOAuthClient->getAccessToken();
	}
	
	private function loadFromSession() {
		$oRequestToken = null;
		$oAccessToken = null;
		if(isset($_SESSION[$this->getSessionName('accesstoken')])) {
			$oAccessToken = unserialize($_SESSION[$this->getSessionName('accesstoken')]);
		}
		if(isset($_SESSION[$this->getSessionName('requesttoken')])) {
			$oRequestToken = unserialize($_SESSION[$this->getSessionName('requesttoken')]);
		}
		return array($oRequestToken, $oAccessToken);
	}
	
	private function getSessionName($sKey) {
		return implode('_', array(
			'gg',
			$this->m_aConfig['consumer_key'],
			$sKey
		));
	}
}

?>