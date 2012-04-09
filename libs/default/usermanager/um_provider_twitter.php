<?php
require_plugin('Socializer');

class ProviderTwitter extends UserConnectionProvider {
	
	private $m_aData = null;
	
	public function getConnectionId() {
		return $this->getConnectionData() ? $this->m_aData['id'] : false;
	}
	
	public function getConnectionData() {
		if($this->getConnectionId()) {
			if($this->m_aData === null) {
				$this->m_aData = Socializer::twitter()->api('/account/verify_credentials.json', 'GET', array('skip_status' => 1));
			}
			return $this->m_aData;
		}
		return false;
	}
	
	public function getConnectionTokens() {
		return '';
	}
}

?>