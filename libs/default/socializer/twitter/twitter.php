<?php
require_plugin('OAuth');

class Twitter {
	
	private $m_aConfig;
	private $m_oConsumer;
	private $m_oProvider;
	
	private $m_oRequestToken;
	private $m_oAccessToken;
	private $m_oOAuthClient;
	
	public function __construct(array $aConfig) {
		$this->m_aConfig = $aConfig;
				
		$this->m_oConsumer = new OAuthConsumer($aConfig['consumer_key'], $aConfig['consumer_secret']);
		
		$this->m_oProvider = new OAuthProvider('Twitter');
		$this->m_oProvider->setSignatureMethod('HMAC-SHA1');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_REQUEST_TOKEN, 'https://api.twitter.com/oauth/request_token');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_REQUEST_TOKEN, 'POST');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_AUTHENTICATE, 'https://api.twitter.com/oauth/authorize');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_AUTHENTICATE, 'GET');
		$this->m_oProvider->setUrl(OAuth::PROVIDER_ACCESS_TOKEN, 'https://api.twitter.com/oauth/access_token');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_ACCESS_TOKEN, 'POST');		
		$this->m_oProvider->setUrl(OAuth::PROVIDER_API, 'https://api.twitter.com/1');
		$this->m_oProvider->setMethod(OAuth::PROVIDER_API, 'GET');
		
		$this->loadFromSession();
		$this->m_oOAuthClient = new OAuthClient($this->m_oProvider, $this->m_oConsumer, $this->m_oRequestToken, $this->m_oAccessToken);
	}
	
	public function __sleep() {
		return array('m_aConfig', 'm_oConsumer', 'm_oProvider');
	}
	
	public function __wakeup() {
		$this->loadFromSession();
		$this->m_oOAuthClient = new OAuthClient($oProvider, $oConsumer, $oRequestToken, $oAccessToken);
	}
	
	public function __destruct() {
		$_SESSION[$this->getSessionName('requesttoken')] = serialize($this->m_oOAuthClient->getRequestToken());
		$_SESSION[$this->getSessionName('accesstoken')] = serialize($this->m_oOAuthClient->getAccessToken());
	}
	
	public function getLoginUrl() {
		return $this->m_oOAuthClient->getAuthorizationUrl(array('oauth_callback' => $this->m_aConfig['callback']));
	}
	
	public function isLoggedIn() {
		return $this->m_oOAuthClient->isAuthorized();
	}
	
	public function login() {
		return $this->m_oOAuthClient->authorize();
	}
	
	public function api($sUrl = null, $sMethod = null, array $aParams = array()) {
		return json_decode($this->m_oOAuthClient->api($sUrl, $sMethod, $aParams), true);
	}
	
	private function loadFromSession() {
		if(isset($_SESSION[$this->getSessionName('accesstoken')])) {
			$this->m_oAccessToken = unserialize($_SESSION[$this->getSessionName('accesstoken')]);
		}
		if(isset($_SESSION[$this->getSessionName('requesttoken')])) {
			$this->m_oRequestToken = unserialize($_SESSION[$this->getSessionName('requesttoken')]);
		}
	}
	
	private function getSessionName($sKey) {
		return implode('_', array(
			'tw',
			$this->m_aConfig['consumer_key'],
			$sKey
		));
	}
}

?>