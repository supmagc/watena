<?php
require_plugin('DatabaseManager');

class Configuration extends Plugin {
	
	private $m_oSectionTable;
	private $m_oDataTable;
	
	public function make() {
		$oConnection = DatabaseManager::getConnection($this->getConfig('DATABASE_CONNECTION', 'default'));
		$oConnection->getTable('config_section');
		$oConnection->getTable('config_data');
	}
	
	public function init() {
		
	}
	
	public static function setValue($sKey, $mValue) {
		$this->getWatena()->getCache()->set($sKey, $mValue);
	}
	
	public static function getValue($sKey, $mDefault) {
		$this->getWatena()->getCache()->get($sKey, $mDefault);
	}
}

?>