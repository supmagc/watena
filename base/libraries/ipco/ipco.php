<?php

// Inline PHP compilation
class IPCO {

	public function createParserFromContent($sIdentifier, &$sContent) {
		return new IPCO_Parser($sIdentifier, $sContent, $this);
	}
	
	public function createParserFromFile($sFilePath) {
		return $this->createParserFromContent($sFilePath, file_get_contents($sFilePath), $this);
	}
	
	public function getClassName($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return 'IPCO_Compiled_' . Encoding::regReplace('[-/\\. ]', '_', $sIdentifier);
	}
}

?>