<?php

class OAuthConsumer {

	private $m_sKey;
	private $m_sSecret;
	private $m_sCallback;

	public function __construct($sKey, $sSecret, $sCallback = NULL) {
		$this->m_sKey = $sKey;
		$this->m_sSecret = $sSecret;
		$this->m_sCallback = $sCallback;
	}

	public function getKey() {
		return $this->m_sKey;
	}

	public function getSecret() {
		return $this->m_sSecret;
	}

	public function __toString() {
		return "OAuthConsumer[key={$this->getKey()},secret={$this->getSecret()}]";
	}
}
?>