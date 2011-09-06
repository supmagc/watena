<?php

interface IPCO_ICallbacks {
	
	public function getFilePathForTemplate($sTemplate);
	
	public function loadTemplateFromTemplate($sTemplate);
	
	public function loadTemplateFromFilePath($sFilePath);	
}

?>