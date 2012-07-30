<?php

abstract class WatenaConfig {

	private $m_sConfigName;
	
	public final function __construct($sConfigName) {
		$this->m_sConfigName = $sConfigName;
	}
	
	public final function getConfigName() {
		return $this->m_sConfigName;
	}
	
	public final function getLibraries() {
		return $this->libraries($this->getConfigName());
	}
	
	public final function getCharset() {
		return $this->charset($this->getConfigName());
	}
	
	public final function getTimeZone() {
		return $this->timeZone($this->getConfigName());
	}
	
	public final function getTimeFormat() {
		return $this->timeFormat($this->getConfigName());
	}
	
	public final function getCacheEngine() {
		return $this->cacheEngine($this->getConfigName());
	}
	
	public final function getCacheExpiration() {
		return $this->cacheExpiration($this->getConfigName());
	}
	
	public final function getLoggerLevel() {
		return $this->loggerLevel($this->getConfigName());
	}
	
	public final function getLoggerProcessors() {
		return $this->loggerProcessors($this->getConfigName());
	}
	
	public final function getVersion() {
		return $this->version($this->getConfigName());
	}
	
	public abstract function libraries($sConfigName);
	public abstract function charset($sConfigName);
	public abstract function timeZone($sConfigName);
	public abstract function timeFormat($sConfigName);
	public abstract function cacheEngine($sConfigName);
	public abstract function cacheExpiration($sConfigName);
	public abstract function loggerLevel($sConfigName);
	public abstract function loggerProcessors($sConfigName);
	public abstract function version($sConfigName);
}

class WatenaLoader {
	
	public static function run($sConfigClass, $sConfigName) {
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
		
		if(!class_exists($sConfigClass)) {
			die('The config-class you specified does not exists.');
		}
		
		if(!in_array('WatenaConfig', class_parents($sConfigClass))) {
			die('The config-class you specified does not implement WatenaConfig.');
		}
				
		// ############################################################
		// BaseSystem inclusions needed for all Watena classes
		// ############################################################
		require_once PATH_BASE . '/system/global.common.php';
		require_once PATH_BASE . '/system/global.requirements.php';
		require_once PATH_BASE . '/system/static.logger.php';
		require_once PATH_BASE . '/system/interface.ilogprocessor.php';
		require_once PATH_BASE . '/system/interface.ilogfilter.php';
		require_once PATH_BASE . '/system/interface.icache.php';
		require_once PATH_BASE . '/system/exception.watception.php';
		require_once PATH_BASE . '/system/exception.webexception.php';
		require_once PATH_BASE . '/system/exception.assureexception.php';
		require_once PATH_BASE . '/system/exception.filepermissionexception.php';
		require_once PATH_BASE . '/system/class.object.php';
		require_once PATH_BASE . '/system/class.echolog.php';
//		require_once PATH_BASE . '/system/class.configurable.php';
		require_once PATH_BASE . '/system/class.cacheable.php';
		require_once PATH_BASE . '/system/class.cachedata.php';
		require_once PATH_BASE . '/system/class.cacheloader.php';
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
		
		if(!defined('NSESSION')) {
			define('SESSION', true);
			session_start();
			// TODO: use the session-guard
		}
		
		if(!defined('NLOGGER')) {
			define('LOGGER', true);
			Logger::registerProcessor(new EchoLog());
			Logger::init(Logger::WARNING);
		}
		
		if(!defined('NWATENA')) {
			define('WATENA', true);
			function watena() {return Watena::getWatena();}
			new Watena(new $sConfigClass($sConfigName), !defined('NMVC'));
		}
	}
}

?>