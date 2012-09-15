<?php

class MappingTest extends PHPUnit_Framework_TestCase {
	
	public function testDefault() {
		$oMapping = watena()->getMapping();
		$this->assertEquals($oMapping->getRoot(), 'http://unknown');
		$this->assertEquals($oMapping->getFull(), 'http://unknown/');
		$this->assertEquals($oMapping->getHost(), 'unknown');
		$this->assertEquals($oMapping->getLocal(), '/');
		$this->assertEquals($oMapping->getOffset(), '');
		$this->assertEquals($oMapping->getPort(), 80);
		$this->assertEquals($oMapping->getUseragent(), 'Unknown');
	}
	
	public function testAbsoluteSubDirectory() {
		$oMapping = new Mapping('/test/a/bla.mine');
		$this->assertEquals($oMapping->getLocal(), '/test/a/bla.mine');
		$this->assertEquals($oMapping->getPart(0), 'test');
		$this->assertEquals($oMapping->getPart(1), 'a');
		$this->assertEquals($oMapping->getPart(2), 'bla.mine');
		$this->assertNull($oMapping->getPart(3));
	}
	
	public function testRelativeSubDirectory() {
		$oMapping = new Mapping('test/a/bla.mine');
		$this->assertEquals($oMapping->getLocal(), '/test/a/bla.mine');
		$this->assertEquals($oMapping->getPart(0), 'test');
		$this->assertEquals($oMapping->getPart(1), 'a');
		$this->assertEquals($oMapping->getPart(2), 'bla.mine');
		$this->assertNull($oMapping->getPart(3));
	}
	
	public function testParams() {
		$oMapping = new Mapping('helloworld?oeps=noope#linkage');
		$this->assertEquals($oMapping->getLocal(), '/helloworld');
		$this->assertEquals($oMapping->getPart(0), 'helloworld');
		$this->assertEquals($oMapping->getFull(), 'http://unknown/helloworld?oeps=noope#linkage');
		$this->assertNull($oMapping->getPart(1));
	}
}

?>