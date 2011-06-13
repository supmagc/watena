<?php

function IPCO_LoadTemplate($sName) {
	
}

// Inline PHP compilation
class IPCO {

	public function getParser($sIdentifier, &$sContent) {
		return new IPCO_Parser($sIdentifier, $sContent, $this);
	}
	
	public function getClassName($sIdentifier) {
		$sIdentifier = Encoding::toLower($sIdentifier);
		return 'IPCO_Compiled_' . Encoding::regReplace('[-/\\. ]', '_', $sIdentifier);
	}
}

?>