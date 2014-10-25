<?php 
class UrlTest extends Test {
	
	private $m_oUrl;
	
	public function setup() {
		$this->m_oUrl = new Url('https://jelle:Pa55w0rd@test.helloworld.co.uk:81/path/second/item?test=123&second=readme#anchortag');
	}
	
	public function testScheme() {
		$this->assertEquals('https', $this->m_oUrl->getScheme());
	}

	public function testUserName() {
		$this->assertEquals('jelle', $this->m_oUrl->getUserName());
	}

	public function testPassword() {
		$this->assertEquals('Pa55w0rd', $this->m_oUrl->getPassword());
	}
	
	public function testHost() {
		$this->assertEquals('test.helloworld.co.uk', $this->m_oUrl->getHost());
	}

	public function testPort() {
		$this->assertEquals(81, $this->m_oUrl->getPort());
	}

	public function testPath() {
		$this->assertEquals('/path/second/item', $this->m_oUrl->getPath());
	}

	public function testParameters() {
		$this->assertEquals('123', $this->m_oUrl->getParameter('test'));
		$this->assertEquals('readme', $this->m_oUrl->getParameter('second'));
		$this->assertEquals(array('test' => '123', 'second' => 'readme'), $this->m_oUrl->getParameters());
	}

	public function testAnchor() {
		$this->assertEquals('anchortag', $this->m_oUrl->getAnchor());
	}
	
	public function teardown() {
		$this->m_oUrl = null;
	}
}
?>