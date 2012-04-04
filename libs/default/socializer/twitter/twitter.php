<?php
require_plugin('OAuth');

class Twitter {
	
	private $m_aConfig;
	private $m_oToken;
	private $m_oConsumer;
	private $m_oProvider;
	
	private $m_oOAuthClient;
	
	public function __construct(array $aConfig) {
		$this->m_aConfig = $aConfig;
		$this->m_oConsumer = new OAuthConsumer($aConfig['consumer_key'], $aConfig['consumer_secret']);
		$this->m_oProvider = new OAuthProvider('Twitter');
		$this->m_oProvider->setSignatureMethod('HMAC-SHA1');
		$this->m_oProvider->setUrl(OAUTH_PROVIDER_REQUEST_TOKEN, 'https://api.twitter.com/oauth/request_token');
		$this->m_oProvider->setMethod(OAUTH_PROVIDER_REQUEST_TOKEN, 'POST');
		$this->m_oProvider->setUrl(OAUTH_PROVIDER_AUTHENTICATE, 'https://api.twitter.com/oauth/authorize');
		$this->m_oProvider->setMethod(OAUTH_PROVIDER_AUTHENTICATE, 'GET');
		$this->m_oProvider->setUrl(OAUTH_PROVIDER_ACCESS_TOKEN, 'https://api.twitter.com/oauth/access_token');
		$this->m_oProvider->setMethod(OAUTH_PROVIDER_ACCESS_TOKEN, 'POST');
		
		$this->m_oOAuthClient = new OAuthClient($this->m_oProvider, $this->m_oConsumer, $this->m_oToken);
	}
	
	public function __sleep() {
		return array('m_aConfig', 'm_oToken', 'm_oConsumer', 'm_oProvider');
	}
	
	public function getLoginUrl() {
		$oRequest = $this->m_oOAuthClient->createRequest(OAUTH_PROVIDER_REQUEST_TOKEN);
		$oRequest->setParameter('oauth_callback', $this->m_aConfig['callback']);
		$aOAuth = $this->m_oOAuthClient->send($oRequest);
		return $this->m_oProvider->getUrl(OAUTH_PROVIDER_AUTHENTICATE) . '?oauth_request=' . $aOAuth['oauth_token'];
	}
}

?>