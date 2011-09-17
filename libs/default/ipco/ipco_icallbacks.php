<?php

interface IPCO_ICallbacks {
	
	public function getFilePathForTemplate($sTemplate);
	
	public function loadProcessorFromTemplate($sTemplate);
	
	public function loadProcessorFromFilePath($sFilePath);	
	
	public function loadParserFromTemplate($sTemplate);
	
	public function loadParserFromFilePath($sFilePath);	
}

?>
