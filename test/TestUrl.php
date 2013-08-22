<?php

class TestUrl extends PHPUnit_Framework_TestCase {
	
	const URL = 'proto://user:pass@example.com:123/path/dir/file.xs?myStr=test&myVal=123#placeHolder';
	
	public function setUp() {
	}
	
	public function testDefault() {
		$oUrl = new Url(self::URL);
		
		$this->assertEquals('proto', $oUrl->getScheme());
		$this->assertEquals('user', $oUrl->getUserName());
		$this->assertEquals('pass', $oUrl->getPassword());
		$this->assertEquals('example.com', $oUrl->getHost());
		$this->assertEquals(123, $oUrl->getPort());
		$this->assertEquals('/path/dir/file.xs', $oUrl->getPath());
		$this->assertEquals('test', $oUrl->getParameter('myStr'));
		$this->assertEquals(123, $oUrl->getParameter('myVal'));
		$this->assertEquals('placeHolder', $oUrl->getAnchor());
		$this->assertEquals(self::URL, $oUrl->toString());
	}
	
	public function testSetAndGet() {
		$oUrl = new Url(self::URL);
		
		$oUrl->setScheme('ftp');
		$this->assertEquals('ftp', $oUrl->getScheme());
		
		$oUrl->setUserName('Jelle Voet@me.io');
		$this->assertEquals('Jelle Voet@me.io', $oUrl->getUserName());
		
		$oUrl->setPassword('zer$://sdc');
		$this->assertEquals('zer$://sdc', $oUrl->getPassword());
		
		$oUrl->setHost('127.0.0.1');
		$this->assertEquals('127.0.0.1', $oUrl->getHost());
		
		$oUrl->setPort('91');
		$this->assertEquals(91, $oUrl->getPort());
		
		$oUrl->setPath('mine/test');
		$this->assertEquals('/mine/test', $oUrl->getPath());
		
		$oUrl->setAnchor('page-link 12');
		$this->assertEquals('page-link 12', $oUrl->getAnchor());
		
		$oUrl->setParameters(array());
		$this->assertEquals('ftp://Jelle%20Voet%40me.io:zer%24%3A%2F%2Fsdc@127.0.0.1:91/mine/test#page-link%2012', $oUrl->toString());
	}
	
	public function tearDown() {
	}
}

?>