<?php namespace Watena\Libs\Base;
require_includeonce(dirname(__FILE__) . '/../dbmanager/dbmanager.php');

class DatabaseManager extends Plugin {
	
	private $m_aConnectionsData = array();
	private static $s_aConnections = array();
	
	public function make(array $aMembers) {
		$aConnections = explode_trim(',', parent::getConfig('CONNECTIONS', ''));
		foreach($aConnections as $sConnection) {
			$this->m_aConnectionsData []= array(
				Encoding::toUpper($sConnection),
				parent::getConfig($sConnection.'_DSN', null),
				parent::getConfig($sConnection.'_USER', null),
				parent::getConfig($sConnection.'_PASS', null)
			);
		}
		
		parent::getLogger()->debug('Database connections found: ' . implode(', ', $aConnections), $this->m_aConnectionsData);
	}
	
	public function init() {
		foreach($this->m_aConnectionsData as $aConnectionData) {
			self::$s_aConnections[$aConnectionData[0]] = DbConnection::assureUniqueDbConnection(
				$aConnectionData[0], 
				$aConnectionData[1], 
				$aConnectionData[2], 
				$aConnectionData[3]
			);
		}
	}
	
	/**
	 * Check if the named connection is available
	 * 
	 * @param string $sIdentifier
	 * 
	 * @returen bool
	 */
	public static function hasConnection($sIdentifier) {
		return isset(self::$s_aConnections[Encoding::toUpper($sIdentifier)]); 
	}
	
	/**
	 * Retrieve the DbConnection as specified by name, or the first one if none matching is found.
	 * 
	 * @param string $sIdentifier
	 * 
	 * @return DbConnection
	 */
	public static function getConnection($sIdentifier = null) {
		$sIdentifier = Encoding::toUpper($sIdentifier);
		return isset(self::$s_aConnections[$sIdentifier]) ? self::$s_aConnections[$sIdentifier] : array_first(self::$s_aConnections);
	}
	
	public function getVersion() {
		return array(
			'major' => 0,
			'minor' => 3,
			'build' => 0,
			'state' => 'beta'
		);
	}
}

?>