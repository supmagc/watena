<?php
require_includeonce(dirname(__FILE__) . '/../dbman/dbman.php');

class DatabaseManager extends Plugin {

	private $m_aConnections = array();
	
	public function init() {
		$aConnections = array_map('trim', explode(';', parent::getConfig('CONNECTIONS', '')));
		parent::getLogger()->info('Database connections found: ' . implode(', ', $aConnections));
	}
	
	public function getVersion() {
		return array(
			'major' => 0,
			'minor' => 1,
			'build' => 1,
			'state' => 'dev'
		);
	}
}

?>