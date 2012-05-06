<?php

class MappingTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['HTTP_USER_AGENT'] = 'PHPUnit';
		$_SERVER['SERVER_PORT'] = 80;
	}
	
	public function testLocalhostCreationNoSlash() {
		$_SERVER['SCRIPT_NAME'] = '/index.php';
		$_SERVER['REDIRECT_URL'] = '';
		$oMapping = new Mapping();
		$this->assertEquals($oMapping->getFull(), 'http://localhost');
		$this->assertEquals($oMapping->getHost(), 'localhost');
		$this->assertEquals($oMapping->getLocal(), '');
		$this->assertEquals($oMapping->getOffset(), '');
		$this->assertEquals($oMapping->getPort(), 80);
		$this->assertEquals($oMapping->getUseragent(), 'PHPUnit');
	}
	
	public function testSubDirectoryCreationNoSlash() {
		$_SERVER['SCRIPT_NAME'] = '/watena/index.php';
		$_SERVER['REDIRECT_URL'] = '/watena';
		$oMapping = new Mapping();
		$this->assertEquals($oMapping->getFull(), 'http://localhost/watena');
		$this->assertEquals($oMapping->getHost(), 'localhost');
		$this->assertEquals($oMapping->getLocal(), '');
		$this->assertEquals($oMapping->getOffset(), '/watena');
		$this->assertEquals($oMapping->getPort(), 80);
		$this->assertEquals($oMapping->getUseragent(), 'PHPUnit');
	}
	
	public function testLocalhostCreationSlash() {
		$_SERVER['SCRIPT_NAME'] = '/index.php';
		$_SERVER['REDIRECT_URL'] = '/';
		$oMapping = new Mapping();
		$this->assertEquals($oMapping->getFull(), 'http://localhost/');
		$this->assertEquals($oMapping->getHost(), 'localhost');
		$this->assertEquals($oMapping->getLocal(), '/');
		$this->assertEquals($oMapping->getOffset(), '');
		$this->assertEquals($oMapping->getPort(), 80);
		$this->assertEquals($oMapping->getUseragent(), 'PHPUnit');
	}
		
	public function testSubDirectoryCreationSlash() {
		$_SERVER['SCRIPT_NAME'] = '/watena/index.php';
		$_SERVER['REDIRECT_URL'] = '/watena/';
		$oMapping = new Mapping();
		$this->assertEquals($oMapping->getFull(), 'http://localhost/watena/');
		$this->assertEquals($oMapping->getHost(), 'localhost');
		$this->assertEquals($oMapping->getLocal(), '/');
		$this->assertEquals($oMapping->getOffset(), '/watena');
		$this->assertEquals($oMapping->getPort(), 80);
		$this->assertEquals($oMapping->getUseragent(), 'PHPUnit');
	}

	public function testRelativeLocalCreation() {
		
	}
}

?>