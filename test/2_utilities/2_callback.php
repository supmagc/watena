<?php
class CallbackTest extends Test {
	
	private $m_oValidCallback;
	private $m_oInvalidCallback;
	
	public function setup() {
		$this->m_oValidCallback = new Callback('DefinedMethod', array('123', 123));
		$this->m_oInvalidCallback = new Callback('UndefinedMethod', array('123', 123));
	}
	
	public function testProcess() {
		$this->m_oValidCallback->process($this);
	}
	
	public function DefinedMethod($sData, $nData) {
		$this->assertEquals("123", $sData);
		$this->assertEquals(123, $nData);
	}
	
	public function teardown() {
		$this->m_oValidCallback = null;
		$this->m_oInvalidCallback = null;
	}
}
?>