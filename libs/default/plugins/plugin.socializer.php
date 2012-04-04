<?php
require_plugin('OAuth');
require_includeonce(dirname(__FILE__) . '/../socializer/index.php');

class Socializer extends Plugin {
	
	private static $s_oFacebook;
	private static $s_oTwitter; 
	
	public function init() {
		if($this->getConfig('FACEBOOK_ENABLED', false)) {
			self::$s_oFacebook = new Facebook(array(
				'appId' => $this->getConfig('FACEBOOK_ID', ''),
				'secret' => $this->getConfig('FACEBOOK_SECRET', '')
			));
		}
		if($this->getConfig('TWITTER_ENABLED', false)) {
			self::$s_oTwitter = new Twitter(array(
				'consumer_key' => $this->getConfig('TWITTER_ID', ''),
				'consumer_secret' => $this->getConfig('TWITTER_SECRET', ''),
				'callback' => $this->getConfig('TWITTER_CALLBACK', '')
			));
		}
	}
	
	public static function facebook() {
		return self::$s_oFacebook;
	}
	
	public static function twitter() {
		return self::$s_oTwitter;
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