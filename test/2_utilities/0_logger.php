<?php
class TestLogProcessor implements ILogProcessor {
	
	private static $s_sExpectIdentifier;
	private static $s_sExpectLevel;
	
	public function loggerProcess($sIdentifier, $nLevel, $sFile, $nLine, $sMessage, array $aData, array $aTrace) {
		if(self::$s_sExpectIdentifier == $sIdentifier && self::$s_sExpectLevel = $nLevel)
			return;
		
		$sMessage = Encoding::replace(array_map(create_function('$a', 'return \'{\'.$a.\'}\';'), array_keys($aData)), array_values($aData), $sMessage);
		printf('%s [%s] %s', Logger::getLevelName($nLevel), $sIdentifier, $sMessage);
	}
	
	public static function setExpect($sIdentifier, $nLevel) {
		self::$s_sExpectIdentifier = $sIdentifier;
		self::$s_sExpectLevel = $nLevel;
	}
	
	public static function clearExpect() {
		self::$s_sExpectIdentifier = '';
		self::$s_sExpectLevel = -1;
	}
}

Logger::init(Logger::WARNING);
Logger::registerProcessor(new TestLogProcessor(), true);

class LoggerTest extends Test {
	
}
?>