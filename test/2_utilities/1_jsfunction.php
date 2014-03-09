<?php
class JSFunctionTest extends Test {

	private $m_oValidFunction = null;
	private $m_oInvalidFunction = null;
	private $m_sValidFunctionStr = '';
	private $m_sInvalidFunctionStr = '';
	
	public function setup() {
		$this->m_oValidFunction = new JSFunction('myValidFunction', array('"Hello" World\'s !', null, true, 12.85));
		$this->m_sValidFunctionStr = 'function() {window[\'myValidFunction\'].apply(this, ["\"Hello\" World\'s !",null,true,12.85]);}';

		TestLogProcessor::setExpect('JSFunction', Logger::WARNING);
		$this->m_oInValidFunction = new JSFunction('9my In\'Valid Function', array('bla' =>'Hello'));
		$this->m_sInValidFunctionStr = 'function() {window[\'9my In\'Valid Function\'].apply(this, ["Hello"]);}';
		TestLogProcessor::clearExpect();
	}
	
	public function testGetFunctionName() {
		$this->assertEquals('myValidFunction', $this->m_oValidFunction->getFunctionName());
		$this->assertEquals('9my In\'Valid Function', $this->m_oInValidFunction->getFunctionName());
	}
	
	public function testGetParameters() {
		$this->assertEquals(array('"Hello" World\'s !', null, true, 12.85), $this->m_oValidFunction->getParameters());
		$this->assertEquals(array('Hello'), $this->m_oInValidFunction->getParameters());
	}
	
	public function testGetFunction() {
		$this->assertEquals($this->m_sValidFunctionStr, $this->m_oValidFunction->getFunction());
		$this->assertEquals($this->m_sInValidFunctionStr, $this->m_oInValidFunction->getFunction());
	}

	public function testGetCallback() {
		$this->assertEquals('var MyCallback = ' . $this->m_sValidFunctionStr . ';', $this->m_oValidFunction->getCallback('MyCallback', true));
		$this->assertEquals('var MyCallback = ' . $this->m_sValidFunctionStr, $this->m_oValidFunction->getCallback('MyCallback', false));
	}

	public function testCallFunction() {
		$this->assertEquals('(' . $this->m_sValidFunctionStr . ')();', $this->m_oValidFunction->callFunction(true));
		$this->assertEquals('(' . $this->m_sValidFunctionStr . ')()', $this->m_oValidFunction->callFunction(false));
	}
	
	public function testToString() {
		$this->assertEquals($this->m_oValidFunction->getFunction(), $this->m_oValidFunction->toString());
		$this->assertEquals($this->m_oValidFunction->getFunction(), '' . $this->m_oValidFunction);
	}
	
	public function teardown() {
	
	}
}
?>