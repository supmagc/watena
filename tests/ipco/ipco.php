<?php

// Inline PHP compilation
class IPCO {
	
	public function load($sIdentifier) {
		$oParser = new IPCO_Parser($sIdentifier, $this);
		$sClassName = $oParser->getClassName();
		file_put_contents(Encoding::stringToLower($sClassName) . '.php', $oParser->parse());

		$oDebug = new stdClass();
		$oDebug->variable = array(array(false, true), false);
		$oDebug->variableNot = true;
		
		include Encoding::stringToLower($sClassName) . '.php';
		$oTemp = new $sClassName();
		$oTemp->componentPush($oDebug);
		
		return $oTemp;
	}
	
	public function getSourcePath($sIdentifier) {
		return "./$sIdentifier.tpl";
	}
	
	public function getClassName($sIdentifier) {
		return 'IPCO_Compiled_' . Encoding::regReplace('[-/\\. ]', '_', $sIdentifier);
	}
}

?>