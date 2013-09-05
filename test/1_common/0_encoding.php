<?php
include realpath(dirname(__FILE__) . '/../../base/system/static.encoding.php');

class EncodingTest extends Test {

	const CHARSET = 'UTF-8';
	const TESTER = 'IÃ±tÃ«rnÃ¢tiÃ´nÃ lizÃ¦tiÃ¸n';
	const TESTER_FIRST5 = 'IÃ±tÃ«r';
	const TESTER_SECOND5 = 'nÃ¢tiÃ´';
	const TESTER_AFTER5 = 'nÃ¢tiÃ´nÃ lizÃ¦tiÃ¸n';
	const TESTER_WHITESPACES = " \r\n\t\0";
	const TESTER_LOWER = 'iÃ±tÃ«rnÃ¢tiÃ´nÃ lizÃ¦tiÃ¸n';
	const TESTER_UPPER = 'IÃ‘TÃ‹RNÃ‚TIÃ”NÃ€LIZÃ†TIÃ˜N';
	
	public function setup() {
		Encoding::init(self::CHARSET);
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
		$this->assertEquals(self::TESTER_FIRST5, Encoding::substring(self::TESTER, 0, 5));
		$this->assertEquals(self::TESTER_SECOND5, Encoding::substring(self::TESTER, 5, 5));
		$this->assertEquals(self::TESTER_AFTER5, Encoding::substring(self::TESTER, 5, 32));
		$this->assertEquals(self::TESTER_AFTER5, Encoding::substring(self::TESTER, 5));
	}
	
	public function testTrim() {
		$this->assertEquals(self::TESTER, Encoding::trim(self::TESTER_WHITESPACES . self::TESTER . self::TESTER_WHITESPACES));
		$this->assertEquals(self::TESTER, Encoding::trim(self::TESTER_WHITESPACES . self::TESTER . self::TESTER_WHITESPACES, self::TESTER_WHITESPACES));
	}
	
	public function testTrimBegin() {
		$this->assertEquals(self::TESTER, Encoding::trimBegin(self::TESTER_WHITESPACES . self::TESTER));
		$this->assertEquals(self::TESTER . self::TESTER_WHITESPACES, Encoding::trimBegin(self::TESTER_WHITESPACES . self::TESTER . self::TESTER_WHITESPACES));
		$this->assertEquals(self::TESTER, Encoding::trimBegin(self::TESTER_WHITESPACES . self::TESTER, self::TESTER_WHITESPACES));
		$this->assertEquals(self::TESTER . self::TESTER_WHITESPACES, Encoding::trimBegin(self::TESTER_WHITESPACES . self::TESTER . self::TESTER_WHITESPACES, self::TESTER_WHITESPACES));
	}
	
	public function testTrimEnd() {
		$this->assertEquals(self::TESTER, Encoding::trimEnd(self::TESTER . self::TESTER_WHITESPACES));
		$this->assertEquals(self::TESTER_WHITESPACES . self::TESTER, Encoding::trimEnd(self::TESTER_WHITESPACES . self::TESTER . self::TESTER_WHITESPACES));
		$this->assertEquals(self::TESTER, Encoding::trimEnd(self::TESTER . self::TESTER_WHITESPACES, self::TESTER_WHITESPACES));
		$this->assertEquals(self::TESTER_WHITESPACES . self::TESTER, Encoding::trimEnd(self::TESTER_WHITESPACES . self::TESTER . self::TESTER_WHITESPACES, self::TESTER_WHITESPACES));
	}
	
	public function testContains() {
		$this->assertTrue(Encoding::contains(self::TESTER, self::TESTER_FIRST5));
		$this->assertTrue(Encoding::contains(self::TESTER, self::TESTER_SECOND5));
		$this->assertFalse(Encoding::contains(self::TESTER, self::TESTER_WHITESPACES));
	}
	
	public function testToLower() {
		$this->assertEquals(self::TESTER_LOWER, Encoding::toLower(self::TESTER));
	}
	
	public function testToUpper() {
		$this->assertEquals(self::TESTER_UPPER, Encoding::toUpper(self::TESTER));
	}
	
	public function testReplace() {
		$this->assertEquals(self::TESTER_SECOND5 . self::TESTER_AFTER5, Encoding::replace(self::TESTER_FIRST5, self::TESTER_SECOND5, self::TESTER));
		$this->assertEquals(self::TESTER_SECOND5 . self::TESTER_AFTER5, Encoding::replace(array(self::TESTER_FIRST5), array(self::TESTER_SECOND5), self::TESTER));
		$this->assertEquals(self::TESTER_SECOND5 . self::TESTER_AFTER5, Encoding::replace(array(self::TESTER_FIRST5), self::TESTER_SECOND5, self::TESTER));
		$this->assertEquals(self::TESTER_SECOND5 . self::TESTER_AFTER5, Encoding::replace(self::TESTER_FIRST5, array(self::TESTER_SECOND5), self::TESTER));
		$this->assertNotEquals(self::TESTER_SECOND5 . self::TESTER_AFTER5, Encoding::replace(self::TESTER_WHITESPACES, self::TESTER_SECOND5, self::TESTER));
	}
	
	public function testBeginsWith() {
		$this->assertTrue(Encoding::beginsWith(self::TESTER, self::TESTER_FIRST5));
		$this->assertFalse(Encoding::beginsWith(self::TESTER, self::TESTER_SECOND5));
		$this->assertFalse(Encoding::beginsWith(self::TESTER, self::TESTER_AFTER5));
		$this->assertFalse(Encoding::beginsWith(self::TESTER, self::TESTER_WHITESPACES));
	}
	
	public function testEndsWith() {
		$this->assertFalse(Encoding::endsWith(self::TESTER, self::TESTER_FIRST5));
		$this->assertFalse(Encoding::endsWith(self::TESTER, self::TESTER_SECOND5));
		$this->assertTrue(Encoding::endsWith(self::TESTER, self::TESTER_AFTER5));
		$this->assertFalse(Encoding::endsWith(self::TESTER, self::TESTER_WHITESPACES));
	}
	
	public function testIndexOf() {
		
	}
	
	public function testLength() {
		
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