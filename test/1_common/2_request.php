<?php
include_once realpath(dirname(__FILE__) . '/../../base/system/static.request.php');

class RequestTest extends Test {
	
	public function setup() {
		Request::init();
	}

	public function testIsHttp() {
		$this->assertTrue(Request::isHttp());
	}
	
	public function testIsHttps() {
		$this->assertFalse(Request::isHttps());
	}
	
	public function testProtocol() {
		if(Request::isHttp())
			$this->assertEquals('http', Request::protocol());
		if(Request::isHttps())
			$this->assertEquals('https', Request::protocol());
	}
	
	public function testHost() {
		$this->assertEquals(Encoding::toLower($_SERVER['HTTP_HOST']), Request::host());
	}
	
	public function testPort() {
		$this->assertEquals(80, Request::port());
	}
	
	public function testBase() {
		$this->assertEquals('http://localhost', Request::base());
	}
	
	public function testOffset() {
		$this->assertEquals('', Request::offset());
	}
	
	public function testRoot() {
		$this->assertEquals('http://localhost', Request::root());
	}
	
	public function testPath() {
		$this->assertEquals('/w_test.php', Request::path());
	}
	
	public function testUrl() {
		$this->assertEquals('http://localhost/w_test.php', Request::url());
	}
	
	public function testMethod() {
		$this->assertEquals('GET', Request::method());
	}
	
	public function testUserAgent() {
		$this->assertEquals($_SERVER['HTTP_USER_AGENT'], Request::userAgent());
	}
}

?>