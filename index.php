<?php
include 'base/watena.php';

dump($_SERVER['HTTP_HOST']);
dump($_SERVER['SERVER_PORT']);
dump($_SERVER['SCRIPT_NAME']);
dump($_SERVER['HTTP_USER_AGENT']);
dump($_SERVER['REDIRECT_URL']);

if(defined('WATENA')) {
	$aConfig = parse_ini_file(PATH_BASE . '/watena.ini', true);
	if(!$aConfig) die('No readable Watena config file could be found to bootstrap Watena!');
	new Watena($aConfig, !defined('NMVC'));
}
?>