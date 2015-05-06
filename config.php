<?php

if(!defined('CONFIG')) {
	if(!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] == 'localhost') {
		define('CONFIG', 'local');
	}
	else {
		define('CONFIG', 'deploy');
	}
}

#
# Initialise the configuration array.
#
$conf = array();

#
# Initialise the default configuration.
# If needed, you can specify specific values for different configurations.
# They will be used according to the $name.
# Missing values will be replaced with the default set.
# Make sure to define the array before setting values.
#
$conf['default'] = array();
$conf['testing'] = array();
$conf['deploy'] = array();
$conf['local'] = array();

#
# The libraries that will be loaded and used.
# Their order can be of some importance if multiple libraries contain
# identical content, or if some libraries depend on each other.
# A rule of thumb can be to define more specific libraries as last.
# (but before the HelloWorld library)
#
$conf['default']['libraries'] = array('default', 'admin', 'portfolio', 'helloworld');

#
# Charset used throughout the system.
# This is used for output-generation ,database-access, input processing, ...
#
$conf['default']['charset'] = 'UTF8';

#
# Default time-zone for date functions.
#
$conf['default']['timezone'] = 'UTC';

#
# Default time-format used for time rendering.
# Specification follows the php date()-function.
# Default: Y/m/d H:i:s (2000/12/31 22:01:01)
#
$conf['default']['timeformat'] = 'Y/m/d H:i:s';

#
# The cache-engine plugin.
# Some defaults are bundled with the default-library:
# - CacheAPC
# - CacheMemcache
# - CachePEAR
#
$conf['default']['cachengine'] = ''; //CacheMemcache';

#
# Default cache expiration time.
# TODO: Are these minutes or seconds ?
#
$conf['default']['cachexpiration'] = 30;

#
# Default log filter-level for newly initiated logprocessors.
# Available values:
# - ALWAYS: Show all
# - INFO: Used for information messages about the processing of the request.
# - DEBUG: Used for debug messages when things get wrong.
# - EXCEPTION: Used for displaying exception messages when not catched.
# - WARNING: Used for warning messages when things happen that should be fixed or clarified.
# - ERROR: Used for error messages when something goes wrong.
# - TERMINATE: Used to terminate the system with a logger message.
# - NONE: Show none
#
$conf['default']['loglevel'] = 'WARNING';

#
# A list with all additionaly loaded logprocessors.
# The echoLog-class will be auto-loaded on an earlier stage.
# If so desired, you can clear the EchoLog-class later on.
#
$conf['default']['logprocessors'] = array();

#
# The webroot offset of the watena install.
# This value shoud never end on '/' and if no webroot is given, just be ''.
#
$conf['default']['webroot'] = '/watena';

#
# The default host/domain for this installation.
#
$conf['default']['domain'] = 'localhost';

#
# Enable the debugging features by default when no other flags are found.
#
$conf['default']['debugdefault'] = false;
$conf['testing']['debugdefault'] = true;
$conf['local']['debugdefault'] = true;

#
# Enable the debugging features when a constant 'DEBUG' can be found.
# ex: define('DEBUG', 1);
#
$conf['default']['debugdefine'] = false;

#
# Enable the debugging features when a session variable 'DEBUG' can be found
# ex: $_SESSION['DEBUG'] = 1;
#
$conf['default']['debugsession'] = false;

#
# Enable the debugging features when a request variable 'DEBUG' can be found.
# This can be POST, GET, or COOKIE data.
# ex: $_REQUEST['DEBUG'] = 1;
# ex: $_COOKIE['DEBUG'] = 1;
# ex: $_POST['DEBUG'] = 1;
# ex: $_GET['DEBUG'] = 1;
#
$conf['default']['debugrequest'] = false;

#
# Try to use output compression when supported by the client.
#
$conf['default']['compression'] = true;

#
# Disable the advanced logging system
#
// define('NLOGGER', false);

#
# Disable the session safeguard
#
// define('NSESSION', false);

#
# Disable the loading of watena
#
// define('NWATENA', false);
?>