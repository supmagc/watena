<?php
require_plugin('Socializer');

class ProviderFacebook extends UserConnectionProvider {
	
	private $m_aData = null;
	
	public function getConnectionId() {
		$nId = Socializer::facebook()->getUser();
		return $nId > 0 ? $nId : false;
	}
	
	public function getConnectionData() {
		if($this->getConnectionId()) {
			if($this->m_aData === null) {
				$this->m_aData = Socializer::facebook()->api('/me');
			}
			return $this->m_aData;
		}
		return false;
	}
	
	public function getConnectionTokens() {
		return Socializer::facebook()->getAccessToken();
	}
}

?>