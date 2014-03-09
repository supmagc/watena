<?php

class AdminModuleGroup extends CacheableDirectory {
	
	private $m_aModules = array();
	
	public final function make(array $aMembers) {
		foreach($this->getFiles("xml", true) as $sModulePath) {
			$this->m_aModules []= AdminModule::createModule($sModulePath);
		}
	}
	
	public static function createModuleGroup($sLibrary) {
		return self::create($sLibrary . '/modules', array(), array());
	}
}

?>