<?php
require_includeonce(dirname(__FILE__) . '/../dbman/dbman.php');

class DatabaseManager extends Plugin {

	private $m_aConnections = array();
	
	public function init() {
		
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