<?php

class CustomerTokens {

}

class OAuthConsumer {
	public $key;
	public $secret;

	function __construct($key, $secret, $callback_url=NULL) {
		$this->key = $key;
		$this->secret = $secret;
		$this->callback_url = $callback_url;
	}

	function __toString() {
		return "OAuthConsumer[key=$this->key,secret=$this->secret]";
	}
}

class OAuthToken {

	// access tokens and request tokens
	public $key;
	public $secret;

	/**
	 * key = the token
	 * secret = the token secret
	 */
	function __construct($key, $secret) {
		$this->key = $key;
		$this->secret = $secret;
	}

	/**
	 * generates the basic string serialization of a token that a server
	 * would respond to request_token and access_token calls with
	 */
	function to_string() {
		return "oauth_token=" .
		OAuthUtil::urlencode_rfc3986($this->key) .
		"&oauth_token_secret=" .
		OAuthUtil::urlencode_rfc3986($this->secret);
	}

	function __toString() {
		return $this->to_string();
	}
}

class OAuth {

	private $m_aParams;
	private $m_aDefaultParams = array(
		'customer_key' => '',
		'customer_secret' => '',
	);

	private $m_sCustomerKey;
	private $m_sCustomerSecret;
	private $m_sOAuthToken;

	public function __construct() {
		$this->getTokens();
	}

	public function getLoginUrl() {
		if(!$this->m_sOAuthToken) {
			$aData = $this->OAuthRequest('', 'POST');
			$this->m_sOAuthToken = $aData['auth_token'];
		}
		return '' . $this->m_sOAuthToken;
	}

	public function isLoggedIn() {

	}

	public function OAuthRequest($sUrl, $sMethod) {
		$aParams = array(
			'oauth_nonce' => MD5(microtime(true)),
			'oauth_callback' => 'http://flandersisafestival.dev/tester.php',
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_consumer_key' => $sKey,
			'oauth_version' => '1.0',
		);

		ksort($aParams);
		$sParams = http_build_query($aParams, null, '&');

		$sBase = $sMethod . '&' . urlencode($sUrl) . '&' . urlencode($sParams);
		$sHash = base64_encode(hash_hmac('sha1', $sBase, $sSecret, true));
		$aParams['oauth_signature'] = $sHash;

		$oRequest = new WebRequest($sUrl, $sMethod);

		$aOAuths = array();
		foreach($aParams as $sKey => $sValue) {
			if(strpos($sKey, 'oauth_') === 0)
			$aOAuths []= urlencode($sKey) . '="' . urlencode($sValue) . '"';
			else
			$oRequest->addField($sKey, $sValue);
		}
		$oRequest->addHeader('Authorization', 'OAuth ' . implode(', ', $aOAuths));

		$aContent = array();
		$sContent = $oRequest->send()->getContent();
		parse_str($sContent, $aContent);
		return $aContent;
	}
}



?>