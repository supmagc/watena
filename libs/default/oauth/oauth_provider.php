<?php

class OAuthProvider {
	
	private $m_sName;
	private $m_sSignatureMethod;
	private $m_aUrls = array();
	private $m_aMethods = array();
	
	public function __construct($sName) {
		$this->m_sName = $sName;
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function getSignatureMethod() {
		return $this->m_sSignatureMethod;
	}
	
	public function getUrl($nType) {
		return isset($this->m_aUrls[$nType]) ? $this->m_aUrls[$nType] : false;
	}
	
	public function getMethod($nType) {
		return isset($this->m_aMethods[$nType]) ? $this->m_aMethods[$nType] : false;
	}
	
	public function setSignatureMethod($sSignatureMethod) {
		$this->m_sSignatureMethod = $sSignatureMethod;
	}
	
	public function setUrl($nType, $sUrl) {
		$this->m_aUrls[$nType] = $sUrl;
	}
	
	public function setMethod($nType, $sMethod) {
		$this->m_aMethods[$nType] = $sMethod;
	}
	
	public function __toString() {
		return "OAuthProvider[name={$this->getName()},signature={$this->getSignatureMethod()}]";
	}
}

?>