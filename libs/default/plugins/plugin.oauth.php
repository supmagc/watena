<?php
require_includeonce(dirname(__FILE__) . '/../oauth/index.php');

class OAuth extends Plugin {
	
	const PROVIDER_REQUEST_TOKEN = 1;
	const PROVIDER_AUTHENTICATE = 2;
	const PROVIDER_ACCESS_TOKEN = 3;
	const PROVIDER_API = 4;
	const PROVIDER_DEAUTHENTICATE = 5;
	
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