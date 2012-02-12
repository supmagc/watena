<?php
require_includeonce(dirname(__FILE__) . '/../dbman/dbman.php');

class DatabaseManager extends Plugin {

	private static $s_aConnections = array();
	
	public function make() {
		$aConnections = explodeTrim(',', parent::getConfig('CONNECTIONS', ''));
		foreach($aConnections as $sConnection) {
			$sConnection = strtoupper($sConnection);
			$oConnection = new DbConnection(parent::getConfig($sConnection.'_DSN', null), parent::getConfig($sConnection.'_USER', null), parent::getConfig($sConnection.'_PASS', null));
			self::$s_aConnections[strtoupper($sConnection)] = $oConnection;
		}
		
		parent::getLogger()->info('Database connections found: ' . implode(', ', $aConnections));
	}
	
	/**
	 * Check if the named connection is available
	 * 
	 * @param string $sConnection
	 * 
	 * @returen bool
	 */
	public static function hasConnection($sConnection) {
		return in_array(strtoupper($sConnection), self::$s_aConnections); 
	}
	
	/**
	 * Retrieve the DbConnection as specified by name
	 * 
	 * @param string $sConnection
	 * 
	 * @return DbConnection
	 */
	public static function getConnection($sConnection) {
		return self::hasConnection($sConnection) ? self::$s_aConnections[strtoupper($sConnection)] : false;
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