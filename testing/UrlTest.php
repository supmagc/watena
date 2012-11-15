<?php

class UrlTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
	}
	
	public function testDefault() {
		$oUrl = new Url('proto://user:pass@example.com:123/path/dir/file.xs?myStr=test&myVal=123#placeHolder');
		
		$this->assertEquals('proto', $oUrl->getScheme());
		$this->assertEquals('user', $oUrl->getUserName());
		$this->assertEquals('pass', $oUrl->getPassword());
		$this->assertEquals('example.com', $oUrl->getHost());
		$this->assertEquals(123, $oUrl->getPort());
		$this->assertEquals('/path/dir/file.xs', $oUrl->getPath());
		$this->assertEquals('test', $oUrl->getParameter('myStr'));
		$this->assertEquals(123, $oUrl->getParameter('myVal'));
		$this->assertEquals('placeHolder', $oUrl->getAnchor());
	}
	
	public function tearDown() {
	}
}

?>