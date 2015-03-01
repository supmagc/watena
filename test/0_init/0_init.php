<?php
define('CONFIG', 'testing');
include realpath(dirname(__FILE__) . '/../../base/watena.php');
WatenaLoader::init();


class InitTest extends Test {
	
	// Stub class used for loading, no tests are defined
}
