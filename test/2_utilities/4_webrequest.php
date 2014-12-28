<?php
class WebRequestTest extends Test {
	
	private $m_oRequest200;
	private $m_oRequest404;
	private $m_oRequestQuery;
	private $m_oResponse200;
	private $m_oResponse404;
	private $m_oResponseQuery;
	
	public function setup() {
		$oUrl200 = new Url('http://www.google.com');
		$oUrl404 = new Url('http://www.google.com/unknown');
		$oUrlQuery = new Url('http://www.google.com/search');
		$oUrlQuery->addParameter('q', 'Hello World');
		$this->m_oRequest200 = new WebRequest($oUrl200);
		$this->m_oRequest404 = new WebRequest($oUrl404);
		$this->m_oRequestQuery = new WebRequest($oUrlQuery);
	}
	
	public function testSend() {
		$this->m_oResponse200 = $this->m_oRequest200->send();
		$this->m_oResponse404 = $this->m_oRequest404->send();
		$this->m_oResponseQuery = $this->m_oRequestQuery->send();
	}
	
	public function testHttpCode() {
		$this->assertEquals(200, $this->m_oResponse200->getHttpCode());
		$this->assertEquals(404, $this->m_oResponse404->getHttpCode());
		$this->assertEquals(200, $this->m_oResponseQuery->getHttpCode());
	}
	
	public function testResponse() {
		echo $this->m_oResponseQuery->getCharset();
		$this->assertEquals('UTF8', $this->m_oResponseQuery->getCharset());
	}
	
	public function teardown() {
		$this->m_oResponse200 = null;
		$this->m_oResponse404 = null;
		$this->m_oRequest200 = null;
		$this->m_oRequest404 = null;
	}
}
?>