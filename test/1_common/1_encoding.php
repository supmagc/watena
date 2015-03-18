<?php
Encoding::init('UTF8');

class EncodingTest extends Test {

	const CHARSET = 'UTF-8';
	const WHITESPACES = " \r\n\t\0";
	
	private $m_sTesterLeft;
	private $m_sTesterMiddle;
	private $m_sTesterRight;
	private $m_sTesterAll;
	private $m_sTesterExcluded;
	private $m_sTesterSearch;
	private $m_sTesterReplace;
	private $m_sTesterReplaced;
	
	public function setup() {
		Encoding::init(self::CHARSET);

		$this->m_sTesterLeft = $this->generateString(5);
		$this->m_sTesterMiddle = $this->generateString(5);
		$this->m_sTesterRight = $this->generateString(5);
		$this->m_sTesterAll = $this->m_sTesterLeft . $this->m_sTesterMiddle . $this->m_sTesterRight;
		
		$this->m_sTesterExcluded = $this->generateString(5, 1, 255);
		
		$this->m_sTesterSearch = $this->m_sTesterMiddle;
		$this->m_sTesterReplace = $this->generateString(5);
		$this->m_sTesterReplaced = $this->m_sTesterLeft . $this->m_sTesterReplace . $this->m_sTesterRight;
	}
	
	private function generateString($nLength, $nFrom = 256, $nTo = 1114110) {
		$sReturn = '';
		for($i=0 ; $i<$nLength ; ++$i) {
			$sReturn .= uchr(rand(1, 1114110));
		}
		return $sReturn;
	}
	
	public function testSettings() {
		$this->assertEquals(self::CHARSET, Encoding::charset());
		$this->assertEquals(self::CHARSET, mb_internal_encoding());
		$this->assertEquals(self::CHARSET, mb_regex_encoding());
		$this->assertEquals(self::CHARSET, ini_get('default_charset'));
	}
	
	public function testConvert() {
	}
	
	public function testConvertByRef() {
	}
	
	public function testSubstring() {
		$this->assertEquals($this->m_sTesterLeft, Encoding::substring($this->m_sTesterAll, 0, 5));
		$this->assertEquals($this->m_sTesterMiddle, Encoding::substring($this->m_sTesterAll, 5, 5));
		$this->assertEquals($this->m_sTesterRight, Encoding::substring($this->m_sTesterAll, 10, 5));
		$this->assertEquals($this->m_sTesterRight, Encoding::substring($this->m_sTesterAll, 10, 10));
		$this->assertEquals($this->m_sTesterRight, Encoding::substring($this->m_sTesterAll, 10));
	}
	
	public function testTrim() {
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trim($this->m_sTesterMiddle . self::WHITESPACES));
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trim(self::WHITESPACES . $this->m_sTesterMiddle));
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trim(self::WHITESPACES . $this->m_sTesterMiddle . self::WHITESPACES));
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trim(self::WHITESPACES . $this->m_sTesterMiddle . self::WHITESPACES, self::WHITESPACES));
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trim($this->m_sTesterMiddle . self::WHITESPACES, self::WHITESPACES));
	}
	
	public function testTrimBegin() {
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trimBegin(self::WHITESPACES . $this->m_sTesterMiddle));
		$this->assertEquals($this->m_sTesterMiddle . self::WHITESPACES, Encoding::trimBegin($this->m_sTesterMiddle . self::WHITESPACES));
		$this->assertEquals($this->m_sTesterMiddle . self::WHITESPACES, Encoding::trimBegin(self::WHITESPACES . $this->m_sTesterMiddle . self::WHITESPACES));
	}
	
	public function testTrimEnd() {
		$this->assertEquals($this->m_sTesterMiddle, Encoding::trimEnd($this->m_sTesterMiddle . self::WHITESPACES));
		$this->assertEquals(self::WHITESPACES . $this->m_sTesterMiddle, Encoding::trimEnd(self::WHITESPACES . $this->m_sTesterMiddle));
		$this->assertEquals(self::WHITESPACES . $this->m_sTesterMiddle, Encoding::trimEnd(self::WHITESPACES . $this->m_sTesterMiddle . self::WHITESPACES));
	}
	
	public function testContains() {
		$this->assertTrue(Encoding::contains($this->m_sTesterAll, $this->m_sTesterLeft));
		$this->assertTrue(Encoding::contains($this->m_sTesterAll, $this->m_sTesterMiddle));
		$this->assertTrue(Encoding::contains($this->m_sTesterAll, $this->m_sTesterRight));
		$this->assertFalse(Encoding::contains($this->m_sTesterAll, $this->m_sTesterExcluded));
	}
	
	public function testReplace() {
		$this->assertEquals($this->m_sTesterReplaced, Encoding::replace($this->m_sTesterSearch, $this->m_sTesterReplace, $this->m_sTesterAll));
		$this->assertEquals($this->m_sTesterReplaced, Encoding::replace(array($this->m_sTesterSearch), $this->m_sTesterReplace, $this->m_sTesterAll));
		$this->assertEquals($this->m_sTesterReplaced, Encoding::replace($this->m_sTesterSearch, array($this->m_sTesterReplace), $this->m_sTesterAll));
		$this->assertEquals($this->m_sTesterReplaced, Encoding::replace(array($this->m_sTesterSearch), array($this->m_sTesterReplace), $this->m_sTesterAll));
	}
	
	public function testBeginsWith() {
		$this->assertTrue(Encoding::beginsWith($this->m_sTesterAll, $this->m_sTesterLeft));
		$this->assertFalse(Encoding::beginsWith($this->m_sTesterAll, $this->m_sTesterMiddle));
		$this->assertFalse(Encoding::beginsWith($this->m_sTesterAll, $this->m_sTesterRight));
	}
	
	public function testEndsWith() {
		$this->assertFalse(Encoding::endsWith($this->m_sTesterAll, $this->m_sTesterLeft));
		$this->assertFalse(Encoding::endsWith($this->m_sTesterAll, $this->m_sTesterMiddle));
		$this->assertTrue(Encoding::endsWith($this->m_sTesterAll, $this->m_sTesterRight));
	}
	
	public function testIndexOf() {
		$this->assertEquals(0, Encoding::indexOf($this->m_sTesterAll, $this->m_sTesterLeft));
		$this->assertEquals(5, Encoding::indexOf($this->m_sTesterAll, $this->m_sTesterMiddle));
		$this->assertEquals(10, Encoding::indexOf($this->m_sTesterAll, $this->m_sTesterRight));
		$this->assertFalse(Encoding::indexOf($this->m_sTesterAll, $this->m_sTesterExcluded));
	}
	
	public function testLength() {
		$this->assertEquals(0, Encoding::length(""));
		$this->assertEquals(5, Encoding::length($this->m_sTesterMiddle));
		$this->assertEquals(15, Encoding::length($this->m_sTesterAll));
	}
	
	public function testRegMatch() {
		
	}
	
	public function testRegFind() {
		
	}
	
	public function testRegFindAll() {
		
	}
	
	public function testRegReplace() {
		
	}
	
	public function testRegReplaceAll() {
		
	}
	
	public function testRegEncode() {
		
	}
}
?>