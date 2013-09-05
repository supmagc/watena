<?php
include realpath(dirname(__FILE__) . '/../../base/system/static.request.php');

class RequestTest extends Test {
	
	public function setup() {
		Request::init();
	}
	
	
}

?>