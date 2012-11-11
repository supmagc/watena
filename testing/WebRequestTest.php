<?php

class WebRequestTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
	}
	
	public function testDefault() {
		$oRequest = new WebRequest('http://www.google.com/search?q=hello world', 'GET');
		
		$this->assertEquals('google.com', $oRequest->getHost());
		
		$oResponse = $oRequest->send();
		
		$this->assertEquals(200, $oResponse->getHttpCode());
		$this->assertGreaterThan(0, $oResponse->getDuration());
		$this->assertGreaterThanOrEqual(0, $oResponse->getRedirects());
	}
	
	public function tearDown() {
	}
}

?>