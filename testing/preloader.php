<?php
require_once realpath(dirname(__FILE__) . '/../base/watena.php');

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTP_USER_AGENT'] = 'PHPUnit';
$_SERVER['SERVER_PORT'] = 80;

class TestConfig extends WatenaConfig {

	public function libraries($sConfigName) {
		return array('default');
	}
	
	public function charset($sConfigName) {
		return 'UTF-8';
	}
	
	public function timeZone($sConfigName) {
		return 'UTC';
	}
	
	public function timeFormat($sConfigName) {
		return 'Y/m/d H:i:s';
	}
	
	public function cacheEngine($sConfigName) {
		return null;
	}
	
	public function cacheExpiration($sConfigName) {
		return 0;
	}
	
	public function loggerLevel($sConfigName) {
		return 'WARNING';
	}
	
	public function loggerProcessors($sConfigName) {
		return array();
	}
	
	public function version($sConfigName) {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 2,
			'state' => 'dev',
			'name' => 'Dusty'
		);
	}
}

WatenaLoader::run('TestConfig', 'testing');
?>