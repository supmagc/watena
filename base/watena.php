<?php
if(!defined('PATH_BASE')) define('PATH_BASE', realpath(dirname(__FILE__)));
if(!defined('PATH_DATA')) define('PATH_DATA', realpath(dirname(__FILE__) . '/../data'));
if(!defined('PATH_LIBS')) define('PATH_LIBS', realpath(dirname(__FILE__) . '/../libs'));
if(!defined('PATH_ROOT')) define('PATH_ROOT', realpath(dirname(__FILE__) . '/..'));

if(!defined('NLOG4PHP')) {
	define('LOG4PHP', true);
}

if(!defined('NEXCEPTIONCATCH')) {
	define('EXCEPTIONCATCH', true);
	function exception_handler(Exception $e) {
		if(is_a($e, 'WatCeption')) {
			echo "<h2>{$e->getDebugMessage()}</h2>";
			echo '<pre>';
			print_r($e->getData());
			echo '</pre>';
			echo "<pre>$e</pre>";
			if($e->getInnerException()) exception_handler($e->getInnerException());
		}
		else {
			echo "<pre>$e</pre>";
		}
		exit;
	}
	set_exception_handler('exception_handler');
}

if(!defined('NERRORTOEXCEPTION')) {
	define('ERRORTOEXCEPTION', true);
	function error_handler($errno, $errstr, $errfile, $errline ) {
	    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	set_error_handler('error_handler');
}

if(!defined('NGLOBAL')) {
	define('GLOBAL', true);
	require_once PATH_BASE . '/system/global.common.php';
}

if(!defined('NWATENA')) {
	define('WATENA', true);
	// ############################################################
	// Base inclusions needed for all Watena classes
	// ############################################################
	require_once PATH_BASE . '/system/interface.icache.php';
	require_once PATH_BASE . '/system/exception.watception.php';
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
	
	// ############################################################
	// Load the application framework
	// ############################################################
	new Watena();
}
?>