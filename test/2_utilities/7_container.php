<?php

class TestContainerItem extends ObjectContained {
	
	private $m_mKey;
	
	public function __construct($mKey) {
		$this->m_mKey = $mKey;
	}
	
	public function getKeyForContainer(Container $oContainer) {
		return $this->m_mKey;
	}
}

class ContainerTest extends Test {
	
	private $m_oContainerA;
	private $m_oContainerB;

	public function setup() {
		$this->m_oContainerA = new Container('A');
		$this->m_oContainerB = new Container('B');
	}
	
	public function testContainerAdd() {
		$oItemA0 = new TestContainerItem('a');
		$oItemA1 = new TestContainerItem('a');
		$oItemB = new TestContainerItem('b');
		
		$this->assertTrue($this->m_oContainerA->addItem($oItemA0));
		$this->assertFalse($this->m_oContainerA->addItem($oItemA0));
		$this->assertFalse($this->m_oContainerA->addItem($oItemA1));
		$this->assertTrue($this->m_oContainerB->addItem($oItemB));
		$this->assertTrue($this->m_oContainerB->addItem($oItemA1));
		
		$this->assertFalse($oItemA1->addToContainer($this->m_oContainerA));
		$this->assertFalse($oItemA0->addToContainer($this->m_oContainerA));
		$this->assertTrue($oItemB->addToContainer($this->m_oContainerA));
		
		$this->assertEquals($oItemA0, $this->m_oContainerA->getItem('a'));
		$this->assertEquals($oItemB, $this->m_oContainerA->getItem('b'));
		$this->assertEquals($oItemA1, $this->m_oContainerB->getItem('a'));
		$this->assertEquals($oItemB, $this->m_oContainerB->getItem('b'));
	}
	
	public function teardown() {
		$this->m_oContainerA = null;
		$this->m_oContainerB = null;
	}
}
