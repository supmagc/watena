<?php
reQUIRE_once(dirname(__FILE__) . '/../socializer/index.php');

class Socializer extends Plugin {
	
	private static $s_oFacebook;
	
	public function init() {
		if($this->getConfig('FACEBOOK_ENABLED', false)) {
			self::$s_oFacebook = new Facebook(array(
				'appId' => $this->getConfig('FACEBOOK_ID', ''),
				'secret' => $this->getConfig('FACEBOOK_SECRET', '')
			));
		}
	}
	
	public static function facebook() {
		return self::$s_oFacebook;
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