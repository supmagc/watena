<?php

$name = 'default';
if(!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] == 'localhost') {
	$name = 'testing';
}
else {
	$name = 'product';
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
$conf['product'] = array();

#
# The libraries that will be loaded and used.
# Their order can be of some importance if multiple libraries contain identical content.
# Priority is given to the libraries that are defined first.
#
$conf['default']['libraries'] = array('admin', 'default');

#
# Charset used throughout the system.
# This is used for output-generation ,database-access, input processing, ...
#
$conf['default']['charset'] = 'UTF-8';

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
# Some defaults ar bundles with the base-system:
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


$conf['default']['loglevel'] = 'WARNING';
$conf['default']['logprocessors'] = array();
$conf['default']['webroot'] = 'watena';

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