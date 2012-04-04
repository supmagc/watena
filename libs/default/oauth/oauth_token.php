<?php

class OAuthToken {

	private $m_sKey;
	private $m_sSecret;

	function __construct($sKey, $sSecret) {
		$this->m_sKey = $sKey;
		$this->m_sSecret = $sSecret;
	}

	public function getKey() {
		return $this->m_sKey;
	}

	public function getSecret() {
		return $this->m_sSecret;
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
?>