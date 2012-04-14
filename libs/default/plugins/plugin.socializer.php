<?php
require_plugin('OAuth');
require_includeonce(dirname(__FILE__) . '/../socializer/index.php');

class Socializer extends Plugin {
	
	private $m_oFacebook;
	private $m_oTwitter; 
	private $m_oGoogle;
	
	public function make() {
		$this->m_oFacebook = $this->getConfig('FACEBOOK_ENABLED', false) ? new Facebook(array(
			'appId' => $this->getConfig('FACEBOOK_ID', ''),
			'secret' => $this->getConfig('FACEBOOK_SECRET', '')
		)) : false;
		$this->m_oTwitter = $this->getConfig('TWITTER_ENABLED', false) ? new Twitter(array(
			'consumer_key' => $this->getConfig('TWITTER_ID', ''),
			'consumer_secret' => $this->getConfig('TWITTER_SECRET', '')
		)) : false;
		$this->m_oGoogle = $this->getConfig('GOOGLE_ENABLED', false) ? new Google(array(
			'consumer_key' => $this->getConfig('GOOGLE_ID', ''),
			'consumer_secret' => $this->getConfig('GOOGLE_SECRET', '')
		)) : false;
	}
	
	public function hasFacebook() {
		return (bool)$this->m_oFacebook;
	}
	
	public function getFacebook() {
		return $this->m_oFacebook;
	}
	
	public function hasTwitter() {
		return (bool)$this->m_oTwitter;
	}
	
	public function getTwitter() {
		return $this->m_oTwitter;
	} 
	
	public function hasGoogle() {
		return (bool)$this->m_oGoogle;
	}
	
	public function getGoogle() {
		return $this->m_oGoogle;
	}
	
	/**
	* Retrieve version information of this plugin.
	* The format is an associative array as follows:
	* 'major' => Major version (int)
	* 'minor' => Minor version (int)
	* 'build' => Build version (int)
	* 'state' => Naming of the production state
	*/
	public function getVersion() {
		return array('major' => 0, 'minor' => 1, 'build' => 1, 'state' => 'dev');
	}
}

?>