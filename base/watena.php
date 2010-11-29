<?php

// ############################################################
// Base inclusions needed for all Watena classes
// ############################################################
require_once PATH_BASE . '/classes/interface.icache.php';
require_once PATH_BASE . '/classes/static.encoding.php';
require_once PATH_BASE . '/classes/class.object.php';
require_once PATH_BASE . '/classes/class.cacheable.php';
require_once PATH_BASE . '/classes/class.context.php';
require_once PATH_BASE . '/classes/class.filter.php';
require_once PATH_BASE . '/classes/class.plugin.php';
require_once PATH_BASE . '/classes/class.mapping.php';
require_once PATH_BASE . '/classes/class.controller.php';
require_once PATH_BASE . '/classes/class.cacheempty.php';
require_once PATH_BASE . '/classes/class.watena.php';

// ############################################################
// Load the application framework
// ############################################################
new Watena();
?>