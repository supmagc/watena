<?php
include 'base/watena.php';

$conf = array();
$conf['default'] = array();

$conf['default']['libraries'] = array('admin', 'default');
$conf['default']['charset'] = 'UTF-8';
$conf['default']['timezone'] = 'UTC';
$conf['default']['timeformat'] = 'Y/m/d H:i:s';
$conf['default']['cachengine'] = 'CacheMemcache';
$conf['default']['cachexpiration'] = 30;
$conf['default']['loglevel'] = 'WARNING';
$conf['default']['logprocessors'] = array();
$conf['default']['webroot'] = 'watena';
$conf['default']['version'] = '0.1.2-dev [Dusty]';

class MyConfig extends WatenaConfig {
	
	public function libraries($sConfigName) {
		if($sConfigName == 'toevla')
			return array('toevla', 'admin', 'default');
		else
			return array('admin', 'default');
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
		return 'CacheMemcache';
	}
	
	public function cacheExpiration($sConfigName) {
		return 30;
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

$sConfigName = 'default';

if(isset($_SERVER['COMPUTERNAME'])) {
	if($_SERVER['COMPUTERNAME'] == 'GRIN2011') $sConfigName = 'grin';
}

if(isset($_SERVER['HTTP_HOST'])) {
	if(strstr($_SERVER['HTTP_HOST'], 'flandersisafestival')) $sConfigName = 'toevla';
}

WatenaLoader::run('MyConfig', $sConfigName);
?>