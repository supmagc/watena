<?php

class MvcClassLoader extends CacheableFile {
	
	private $m_sExtends;
	
	public function init() {
		$sContent = parent::getFileData();
	}
	
	public function wakeup() {
		CacheableData::create()
		include_once parent::getFilePath();
	}
	
	public static final function getMvcClassInstance($sType, $sName) {
		
	}
}

?>