<?php
require_plugin('DatabaseManager');

class DatabaseManagerTest extends Test {
	
	private $m_oConnection;
	
	public function setup() {
		$this->m_oConnection = DatabaseManager::getConnection('testing');
	}
	
	public function testConnection() {
		$this->assertType('DbConnection', $this->m_oConnection);
	}

	public function testConnectionSerializable() {
		$sSerialized = serialize($this->m_oConnection);
		$this->m_oConnection = unserialize($sSerialized);
		$this->assertType('DbConnection', $this->m_oConnection);
	}
	
	public function teardown() {
		$this->m_oConnection = null;
	}
}
