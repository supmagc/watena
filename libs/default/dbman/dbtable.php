<?php

class DbTable {

	private $m_sConnection;
	private $m_oConnection;
	private $m_sTable;
	private $m_mIdField;
	
	public function __construct(DbConnection $oConnection, $sTable, $mIdField) {
		$this->m_oConnection = $oConnection;
		$this->m_sTable = $sTable;
		$this->m_mIdField = $mIdField;
	}

	public function __sleep() {
		return array('m_sConnection', 'm_sTable', 'm_mIdField');
	}
	
	public function __wakeup() {
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
	 * @return string|array
	 */
	public function getIdField() {
		return $this->m_mIdField;
	}

	public function select($mId = null) {
		return $this->getConnection()->select($this->getTable(), $mId, $this->getIdField());
	}
	
	public function insert(array $aValues) {
		$nReturn = $this->getConnection()->insert($this->getTable(), $aValues);
		return isset($aValues[$this->getIdField()]) ? $aValues[$this->getIdField()] : $nReturn;
	}
	
	public function update(array $aValues, $mId) {
		return $this->getConnection()->update($this->getTable(), $aValues, $mId, $this->getIdField());
	}
	
	public function delete($mId) {
		return $this->getConnection()->delete($this->getTable(), $mId, $this->getIdField());
	}
}

?>