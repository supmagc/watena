<?php

// Inline PHP compilation
class IPCO {
	
	public function load($sFileName) {
		$oParser = new IPCO_Parser($sFileName, $this);
		file_put_contents('compiled.php', $oParser->parse());
		
		include 'compiled.php';
		new  _Source_CV();
	}
	
	public function getSourcePath($sFileName) {
		return "./$sFileName.tpl";
	}
}

?>