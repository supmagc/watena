<?php
require_model('HtmlModel');
require_plugin('UserManager');

class MainModel extends HtmlModel {
	
	private $m_sHash = null;
	
	public function setHash($sHash) {
		$this->m_sHash = $sHash;
	}
	
	public function hasHash() {
		return $this->m_sHash !== null;
	}
	
	public function getHash() {
		return $this->m_sHash;
	}

	public function getUrl() {
		return $this->getWatena()->getMapping()->getRoot();
	}

	public function hasTwitterLogin() {
		return (bool)UserManager::getProviderTwitter(); 
	}
		
	public function getTwitterLoginUrl() {
		return UserManager::getProviderTwitter()->getConnectUrl(new Mapping('/twitter/callback'));
	}
	
	public function hasFacebookLogin() {
		return (bool)UserManager::getProviderFacebook();
	}
	
	public function getFacebookLoginUrl() {
		return UserManager::getProviderFacebook()->getConnectUrl(new Mapping('/facebook/callback'));
	}
}

?>