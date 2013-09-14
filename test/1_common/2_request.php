<?php
include_once realpath(dirname(__FILE__) . '/../../base/system/static.request.php');

class RequestTest extends Test {
	
	private $m_sProtocol;
	private $m_nProtocolPort;
	private $m_sHost;
	private $m_nPort;
	private $m_sOffset;
	
	public function setup() {
		$this->m_sProtocol = 'http';
		$this->m_nProtocolPort = 80;
		$this->m_sHost = Encoding::toLower($_SERVER['HTTP_HOST']);
		$this->m_nPort = (int)$_SERVER['SERVER_PORT'];
		$this->m_sOffset = '/watena'; // TODO: make this variable
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$this->m_sProtocol = 'https';
			$this->m_nProtocolPort = 443;
		}
		
		Request::init();
	}

	public function testIsHttp() {
		if('http' == $this->m_sProtocol && $this->assertTrue(Request::isHttp())) {
			$this->assertEquals('http', Request::protocol());
		}
	}
	
	public function testIsHttps() {
		if('https' == $this->m_sProtocol && $this->assertTrue(Request::isHttps())) {
			$this->assertEquals('https', Request::protocol());
		}
	}
	
	public function testProtocol() {
		$this->assertEquals($this->m_sProtocol, Request::protocol());
	}
	
	public function testHost() {
		$this->assertEquals($this->m_sHost, Request::host());
	}
	
	public function testPort() {
		$this->assertEquals($this->m_nPort, Request::port());
	}
	
	public function testBase() {
		$sHost = $this->m_sProtocol . '://' . $this->m_sHost;
		if($this->m_nPort != $this->m_nProtocolPort) $sHost .= ':' . $this->m_nPort;
		$this->assertEquals($sHost, Request::base());
	}
	
	public function testOffset() {
		$this->assertEquals($this->m_sOffset, Request::offset());
	}
	
	public function testRoot() {
		$sRoot = $this->m_sProtocol . '://' . $this->m_sHost;
		if($this->m_nPort != $this->m_nProtocolPort) $sRoot .= ':' . $this->m_nPort;
		$sRoot .= $this->m_sOffset;
		$this->assertEquals($sRoot, Request::root());
	}
	
	public function testPath() {
		$this->assertEquals('/w_test.php', Request::path());
	}
	
	public function testUrl() {
		$sUrl = $this->m_sProtocol . '://' . $this->m_sHost;
		if($this->m_nPort != $this->m_nProtocolPort) $sUrl .= ':' . $this->m_nPort;
		$sUrl .= $this->m_sOffset . '/w_test.php';
		$this->assertEquals($sUrl, Request::url());
	}
	
	public function testMethod() {
		$this->assertEquals('GET', Request::method());
	}
	
	public function testUserAgent() {
		$this->assertEquals($_SERVER['HTTP_USER_AGENT'], Request::userAgent());
	}
}

?>