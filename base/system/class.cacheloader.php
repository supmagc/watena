<?php

class CacheLoader extends Object {
	
	public final function __construct($sObject, $sIdentifier = null) {
		if(!class_exists($sObject)) $this->getLogger()->error($sMessage);
	}
	
	public function addFileDependency($sFilePath) {
		
	}
	
	public function addDirectoryDependency($sFilePath) {
		
	}
	
	public function get() {
		
	}
}

?>