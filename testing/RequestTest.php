<?php

class RequestTest extends PHPUnit_Framework_TestCase {
	
	public function testIsHttps() {
		$this->assertFalse(Request::isHttps());
	}
	
	public function testProtocol() {
		$this->assertEquals('http', Request::protocol());
	}
	
	public function testHost() {
		$this->assertEquals('localhost', Request::host());
	}
	
	public function testPort() {
		$this->assertEquals(80, Request::port());
	}
	
	public function testBase() {
		
	}
	
	public function testOffset() {
		$this->assertEquals('', Request::offset());
	}
	
	public function testRoot() {
		
	}
	
	public function testUrl() {
		
	}

	public function testMethod() {
		$this->assertEquals('GET', Request::method());
	}
	
	public function testUserAgent() {
		$this->assertEquals('watena', Request::userAgent());
	}
}

?>