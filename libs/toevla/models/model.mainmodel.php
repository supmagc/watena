<?php
require_model('HtmlModel');
require_plugin('UserManager');
require_plugin('ToeVla');

class MainModel extends HtmlModel {
	
	private $m_sHash = null;
	private $m_nHubId = null;
	private $m_nFestivalId = null;
	
	public function setHash($sHash) {
		$this->m_sHash = $sHash;
	}
	
	public function setHubId($nId) {
		$this->m_nHubId = $nId;
	}
	
	public function setFestivalId($nId) {
		$this->m_nFestivalId = $nId;
	}
	
	public function hasHash() {
		return $this->m_sHash !== null;
	}
	
	public function getHash() {
		return $this->m_sHash;
	}
	
	public function getHubId() {
		return $this->m_nHubId;
	}
	
	public function getFestivalId() {
		return $this->m_nFestivalId;
	}
	
	public function getAnalytics() {
		return $this->getWatena()->getContext()->getPlugin('ToeVla')->getConfig('analytics');
	}
	
	public function getUrl() {
		return $this->getWatena()->getMapping()->getRoot();
	}

	public function hasTwitterLogin() {
		return UserManager::getProviderTwitter(); 
	}
		
	public function getTwitterLoginUrl() {
		return UserManager::getProviderTwitter()->getConnectUrl(new Mapping('/twitter/callback'));
	}
	
	public function hasFacebookLogin() {
		return UserManager::getProviderFacebook();
	}
	
	public function getFacebookLoginUrl() {
		return UserManager::getProviderFacebook()->getConnectUrl(new Mapping('/facebook/callback'));
	}
}

?>