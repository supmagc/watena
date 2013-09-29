<?php

class WatenaLoader {
	
	private static function getConfig() {
		if(($sPath = getcwd() . DIRECTORY_SEPARATOR . 'config.php') && is_readable($sPath)) {}
		else if(($sPath = '/etc/watena/config.php') && is_readable($sPath)) {}
		else if(($sPath = 'C:\\Windows\\watena.php') && is_readable($sPath)) {}
		else if(($sPath = realpath(dirname(__FILE__) . '/../config.php')) && is_readable($sPath)) {}
		
		if(!empty($sPath)) include $sPath;
		if(!isset($conf)) $conf = array();
		if(!isset($name)) $name = WatenaConfig::CONFIGNAME_DEFAULT;
		return new WatenaConfig($conf, $name);
	}
	
	/**
	 * Initialise the watena environment.
	 * 
	 * This effectively sets the required constants:
	 * - PATH_BASE = The path to the base directory.
	 * - PATH DATA = The path to the data directory.
	 * - PATH_LIBS = The path to the installed libraries.
	 * - PATH_ROOT = The path to the root of the application.
	 */
	public static function init() {
		if(!defined('PATH_BASE')) define('PATH_BASE', realpath(dirname(__FILE__)));
		if(!defined('PATH_DATA')) define('PATH_DATA', realpath(dirname(__FILE__) . '/../data'));
		if(!defined('PATH_LIBS')) define('PATH_LIBS', realpath(dirname(__FILE__) . '/../libs'));
		if(!defined('PATH_ROOT')) define('PATH_ROOT', realpath(dirname(__FILE__) . '/..'));

		require_once PATH_BASE . '/system/global.common.php';
		require_once PATH_BASE . '/system/global.requirements.php';
		require_once PATH_BASE . '/system/static.encoding.php';
		require_once PATH_BASE . '/system/static.logger.php';
		require_once PATH_BASE . '/system/static.request.php';
		require_once PATH_BASE . '/system/interface.ilogprocessor.php';
		require_once PATH_BASE . '/system/interface.ilogfilter.php';
		require_once PATH_BASE . '/system/interface.icache.php';
		require_once PATH_BASE . '/system/exception.watception.php';
		require_once PATH_BASE . '/system/exception.webexception.php';
		require_once PATH_BASE . '/system/exception.assureexception.php';
		require_once PATH_BASE . '/system/exception.filepermissionexception.php';
		require_once PATH_BASE . '/system/class.object.php';
		require_once PATH_BASE . '/system/class.watenaconfig.php';
		require_once PATH_BASE . '/system/class.echolog.php';
		require_once PATH_BASE . '/system/class.cacheable.php';
		require_once PATH_BASE . '/system/class.cacheablefile.php';
		require_once PATH_BASE . '/system/class.cacheabledirectory.php';
		require_once PATH_BASE . '/system/class.cachedata.php';
		require_once PATH_BASE . '/system/class.cacheloader.php';
		require_once PATH_BASE . '/system/class.cacheloaderfile.php';
		require_once PATH_BASE . '/system/class.cacheloaderdirectory.php';
		require_once PATH_BASE . '/system/class.cacheabledata.php';
		require_once PATH_BASE . '/system/class.context.php';
		require_once PATH_BASE . '/system/class.requirementbuffer.php';
		require_once PATH_BASE . '/system/class.filter.php';
		require_once PATH_BASE . '/system/class.filterdata.php';
		require_once PATH_BASE . '/system/class.filterrule.php';
		require_once PATH_BASE . '/system/class.filtergroup.php';
		require_once PATH_BASE . '/system/class.plugin.php';
		require_once PATH_BASE . '/system/class.url.php';
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
		require_once PATH_BASE . '/system/class.iniparser.php';
		require_once PATH_BASE . '/system/class.mail.php';
		require_once PATH_BASE . '/system/class.html2text.php';
	}

	/**
	 * Load the system.
	 * 
	 * The following defines may impact the behaviour:
	 * - NSESSION = Disable the session guard. 
	 * - NLOGGER = Disable the default loggers.
	 * - NWATENA = Disable the creation of the main Watena-instance.
	 * 
	 * @return Watena The main Watena-instance of null when NWATENA is defined.
	 */
	public static function load() {
		if(!PATH_BASE || !PATH_DATA || !PATH_LIBS || !PATH_ROOT) {
			die('Not all path-constants are defined.');
		}
		
		if(function_exists('__autoload')) {
			die('You are not allowed to define __autoload(); since a part of the framework depends on it.');
		}
				
		// Get configuration object
		$oConf = self::getConfig();
		
		if(!defined('NSESSION')) {
			define('SESSION', true);
			session_start();
		}
		
		if(!defined('NLOGGER')) {
			define('LOGGER', true);
			Logger::registerProcessor(new EchoLog());
			Logger::init(Logger::WARNING);
		}
		
		if(!defined('NWATENA')) {
			define('WATENA', true);
			function watena() {return Watena::getWatena();}
			return new Watena($oConf, !defined('NMVC'));
		}
		else {
			return null;
		}
	}
}

?>