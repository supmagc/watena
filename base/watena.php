<?php
if(!defined('PATH_BASE')) define('PATH_BASE', realpath(dirname(__FILE__)));
if(!defined('PATH_DATA')) define('PATH_DATA', realpath(dirname(__FILE__) . '/../data'));
if(!defined('PATH_LIBS')) define('PATH_LIBS', realpath(dirname(__FILE__) . '/../libs'));
if(!defined('PATH_ROOT')) define('PATH_ROOT', realpath(dirname(__FILE__) . '/..'));

if(!PATH_BASE || !PATH_DATA || !PATH_LIBS || !PATH_ROOT) {
	die('Not all path-constants are defined.');
}

if(function_exists('__autoload')) {
	die('You are not allowed to define __autoload(); since a part of the framework depends on it.');
}

if(!defined('NSESSION')) {
	define('SESSION', true);
	session_start();
	// TODO: use the session-guard
}

if(!defined('NCOMMON')) {
	define('COMMON', true);
	require_once PATH_BASE . '/system/global.common.php';
}

if(!defined('NLOGGER')) {
	define('LOGGER', true);
	require_once PATH_BASE . '/system/interface.ilogprocessor.php';
	require_once PATH_BASE . '/system/interface.ilogfilter.php';
	require_once PATH_BASE . '/system/static.logger.php';
	require_once PATH_BASE . '/system/class.echolog.php';
	Logger::registerProcessor(new EchoLog());
	Logger::init();
}

if(!defined('NWATENA')) {
	if(defined('NLOGGER')) die('Watena depends on Logger, remove the NLOGGER-constant');
	if(defined('NCOMMON')) die('Watena depends on Global, remove the NCOMMON-constant');
	
	define('WATENA', true);
	
	// ############################################################
	// Base inclusions needed for all Watena classes
	// ############################################################
	require_once PATH_BASE . '/system/global.requirements.php';
	require_once PATH_BASE . '/system/interface.icache.php';
	require_once PATH_BASE . '/system/exception.watception.php';
	require_once PATH_BASE . '/system/exception.webexception.php';
	require_once PATH_BASE . '/system/exception.assureexception.php';
	require_once PATH_BASE . '/system/exception.filepermissionexception.php';
	require_once PATH_BASE . '/system/class.object.php';
	require_once PATH_BASE . '/system/class.configurable.php';
	require_once PATH_BASE . '/system/class.cacheable.php';
	require_once PATH_BASE . '/system/class.cacheabledata.php';
	require_once PATH_BASE . '/system/class.cacheablefile.php';
	require_once PATH_BASE . '/system/class.cacheabledirectory.php';
	require_once PATH_BASE . '/system/class.context.php';
	require_once PATH_BASE . '/system/class.requirementbuffer.php';
	require_once PATH_BASE . '/system/class.filter.php';
	require_once PATH_BASE . '/system/class.filtergroup.php';
	require_once PATH_BASE . '/system/class.plugin.php';
	require_once PATH_BASE . '/system/class.mapping.php';
	require_once PATH_BASE . '/system/class.model.php';
	require_once PATH_BASE . '/system/class.view.php';
	require_once PATH_BASE . '/system/class.controller.php';
	require_once PATH_BASE . '/system/class.cacheempty.php';
	require_once PATH_BASE . '/system/class.watena.php';
	require_once PATH_BASE . '/system/class.datafile.php';
	require_once PATH_BASE . '/system/class.time.php';
	require_once PATH_BASE . '/system/class.interval.php';
	require_once PATH_BASE . '/system/class.upload.php';
	require_once PATH_BASE . '/system/class.webrequest.php';
	require_once PATH_BASE . '/system/class.webresponse.php';
	require_once PATH_BASE . '/system/class.zipfile.php';
	require_once PATH_BASE . '/system/class.inifile.php';
	require_once PATH_BASE . '/system/class.mail.php';
	require_once PATH_BASE . '/system/class.html2text.php';
	
	// ############################################################
	// Load the application framework
	// ############################################################
	if(defined('WATENA')) {
		function watena() {
			return Watena::getWatena();
		}
		$aConfig = parse_ini_file(PATH_BASE . '/watena.ini');
		if(!$aConfig) die('No readable Watena config file could be found to bootstrap Watena!');
		new Watena($aConfig, defined('CONFIG') ? CONFIG : 'default', !defined('NMVC'));
	}
}
?>