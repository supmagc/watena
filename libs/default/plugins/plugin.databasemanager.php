<?php
require_includeonce(dirname(__FILE__) . '/../dbmanager/dbmanager.php');

class DatabaseManager extends Plugin {

	private $m_aConnections = array();
	private static $s_oSingleton;
	
	public function make(array $aMembers) {
		$aConnections = explode_trim(',', parent::getConfig('CONNECTIONS', ''));
		foreach($aConnections as $sConnection) {
			$sConnection = strtoupper($sConnection);
			$oConnection = new DbConnection(parent::getConfig($sConnection.'_DSN', null), parent::getConfig($sConnection.'_USER', null), parent::getConfig($sConnection.'_PASS', null));
			$this->m_aConnections[strtoupper($sConnection)] = $oConnection;
		}
		
		parent::getLogger()->debug('Database connections found: ' . implode(', ', $aConnections));
	}
	
	public function init() {
		self::$s_oSingleton = $this;
	}
	
	/**
	 * Check if the named connection is available
	 * 
	 * @param string $sConnection
	 * 
	 * @returen bool
	 */
	public static function hasConnection($sConnection) {
		return isset(self::$s_oSingleton->m_aConnections[strtoupper($sConnection)]); 
	}
	
	/**
	 * Retrieve the DbConnection as specified by name
	 * 
	 * @param string $sConnection
	 * 
	 * @return DbConnection
	 */
	public static function getConnection($sConnection = null) {
		if($sConnection)
			return self::hasConnection($sConnection) ? self::$s_oSingleton->m_aConnections[strtoupper($sConnection)] : false;
		else
			return array_first(self::$s_oSingleton->m_aConnections);
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