<?php
class WebRequestTest extends Test {
	
	private $m_oRequest;
	private $m_oResponse;
	
	public function setup() {
		$oUrl = new Url('http://www.google.com');
		$this->m_oRequest = new WebRequest($oUrl);
	}
	
	public function testSend() {
		$this->m_oResponse = $this->m_oRequest->send();
	}
	
	public function testHttpCode() {
		$this->assertEquals(200, $this->m_oResponse->getHttpCode());
	}
	
	public function teardown() {
		$this->m_oResponse = null;
		$this->m_oRequest = null;
	}
}
?>