<?php

interface IPCO_ICallbacks {
	
	public function getFilePathForTemplate($sTemplate);
	public function getTemplateClassForFilePath($sFilePath);
}

?>
