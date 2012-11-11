<?php

class MappingTest extends PHPUnit_Framework_TestCase {
	
	public function setUp() {
		
	}
	
	public function testDefault() {
		$oMapping = watena()->getMapping();
		$this->assertEquals('http://unknown', $oMapping->getRoot());
		$this->assertEquals('http://unknown/', $oMapping->getFull());
		$this->assertEquals('unknown', $oMapping->getHost());
		$this->assertEquals('/', $oMapping->getLocal());
		$this->assertEquals('', $oMapping->getOffset());
		$this->assertEquals(80, $oMapping->getPort());
		$this->assertEquals('Unknown', $oMapping->getUseragent());
	}
	
	public function testAbsoluteSubDirectory() {
		$oMapping = new Mapping('/test/a/bla.mine');
		$this->assertEquals('/test/a/bla.mine', $oMapping->getLocal());
		$this->assertEquals('test', $oMapping->getPart(0));
		$this->assertEquals('a', $oMapping->getPart(1));
		$this->assertEquals('bla.mine', $oMapping->getPart(2));
		$this->assertNull($oMapping->getAnchor());
		$this->assertNull($oMapping->getPart(3));
	}
	
	public function testRelativeSubDirectory() {
		$oMapping = new Mapping('test/a/bla.mine');
		$this->assertEquals('/test/a/bla.mine', $oMapping->getLocal());
		$this->assertEquals('test', $oMapping->getPart(0));
		$this->assertEquals('a', $oMapping->getPart(1));
		$this->assertEquals('bla.mine', $oMapping->getPart(2));
		$this->assertNull($oMapping->getAnchor());
		$this->assertNull($oMapping->getPart(3));
	}
	
	public function testParams() {
		$oMapping = new Mapping('helloworld?oeps=noope#linkage');
		$this->assertEquals('/helloworld', $oMapping->getLocal());
		$this->assertEquals('helloworld', $oMapping->getPart(0));
		$this->assertEquals('noope', $oMapping->getParam('oeps'));
		$this->assertEquals('linkage', $oMapping->getAnchor());
		$this->assertEquals('http://unknown/helloworld?oeps=noope#linkage', $oMapping->getFull());
		$this->assertNull($oMapping->getParam('unknown'));
		$this->assertNull($oMapping->getPart(1));
	}
	
	public function tearDown() {
		
	}
}

?>