<?php

// Inline PHP compilation
class IPCO {
	
	public function load($sIdentifier, $mComponent) {
		$oParser = new IPCO_Parser($sIdentifier, $this);
		$sClassName = $oParser->getClassName();
		file_put_contents(Encoding::stringToLower($sClassName) . '.php', $oParser->parse());
		
		include Encoding::stringToLower($sClassName) . '.php';
		$oTemp = new $sClassName($this);
		$oTemp->componentPush($mComponent);
		
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