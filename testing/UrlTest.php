<?php

class UrlTest extends PHPUnit_Framework_TestCase {
	
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
		
		$oUrl->setUserName('Jelle Voet');
		$this->assertEquals('Jelle Voet', $oUrl->getUserName());
		
		$oUrl->setUserName('Jelle Voet');
		$this->assertEquals('Jelle Voet', $oUrl->getUserName());
		
		$oUrl->setUserName('Jelle Voet');
		$this->assertEquals('Jelle Voet', $oUrl->getUserName());
		
		$oUrl->setUserName('Jelle Voet');
		$this->assertEquals('Jelle Voet', $oUrl->getUserName());
		
		$oUrl->setUserName('Jelle Voet');
		$this->assertEquals('Jelle Voet', $oUrl->getUserName());
		
		$oUrl->setUserName('Jelle Voet');
		$this->assertEquals('Jelle Voet', $oUrl->getUserName());
		
		$this->assertEquals('ftp://Jelle', $oUrl->toString());
	}
	
	public function tearDown() {
	}
}

?>