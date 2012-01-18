<?php
require_includeonce(dirname(__FILE__) . '/../dbman/dbman.php');

class DatabaseManager extends Plugin {

	private $m_aConnections = array();
	
	public function make() {
		$aConnections = array_map('trim', explode(';', parent::getConfig('CONNECTIONS', '')));
		foreach($aConnections as $sConnection) {
			$sConnection = strtoupper($sConnection);
			$oConnection = new DbConnection(parent::getConfig($sConnection.'_DSN', null), parent::getConfig($sConnection.'_USER', null), parent::getConfig($sConnection.'_PASS', null));
			$this->m_aConnections[strtoupper($sConnection)] = $oConnection;
		}
		
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