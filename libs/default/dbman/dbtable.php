<?php

class DbTable {

	private $m_sConnection;
	private $m_oConnection;
	private $m_sTable;
	private $m_sIdField;
	
	public function __construct(DbConnection $oConnection, $sTable, $sIdField) {
		$this->m_oConnection = $oConnection;
		$this->m_sTable = $sTable;
		$this->m_sIdField = $sIdField;
	}

	public function __sleep() {
		return array('m_sConnection', 'm_sTable', 'm_sIdField');
	}
	
	public function __wakeup() {
		require_plugin('DatabaseManager');
		$this->m_oConnection = DatabaseManager::getConnection($this->m_sConnection);
	}
	
	/**
	 * Retrieve the internal DbConnection.
	 * 
	 * @return DbConnection
	 */
	public function getConnection() {
		return $this->m_oConnection;
	}

	/**
	 * Retrieve the internal table-name
	 * 
	 * @return string
	 */
	public function getTable() {
		return $this->m_sTable;
	}

	/**
	 * Retrieve the internal idField-name
	 * 
	 * @return string
	 */
	public function getIdField() {
		return $this->m_sIdField;
	}
	
	public function insert(array $aValues) {
		return $this->getConnection()->insert($this->getTable(), $aValues);
	}
	
	public function update($mId, array $aValues) {
		return $this->getConnection()->update($this->getTable(), $mId, $aValues, $this->getIdField());
	}
	
	public function delete($mId) {
		return $this->getConnection()->delete($this->getTable(), $mId, $this->getIdField());
	}
}

?>