<?php
include 'base/watena.php';

if(defined('WATENA')) {
	$aConfig = parse_ini_file(PATH_BASE . '/watena.ini', true);
	if(!$aConfig) die('No readable Watena config file could be found to bootstrap Watena!');
	new Watena($aConfig, !defined('NMVC'));
}
?>