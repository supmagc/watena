<?php namespace Watena\Libs\Base;

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

	function __toString() {
		return "OAuthToken[key={$this->getKey()},secret={$this->getSecret()}]";
	}
}
?>