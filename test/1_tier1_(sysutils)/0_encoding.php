<?php
include realpath(dirname(__FILE__) . '/../../base/system/static.encoding.php');

class EncodingTest extends Test {

	public function setup() {
		Encoding::init('UTF-8');
	}
}
?>