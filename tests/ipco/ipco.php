<?php

// Inline PHP compilation
class IPCO {
	
	public function load($sFileName) {
		$oFile = new IPCO_File($this);
	}
	
	public function getSourcePath($sFileName) {
		return "./$sFileName.tpl";
	}
}

?>