<?php
/**
 * Serializable database table representation.
 * Each row is identifiable by a single column.
 * 
 * @author Jelle Voet
 * @version 0.2.1
 *
 */
class DbTable extends Object {

	private $m_sConnection;
	private $m_oConnection;
	private $m_sTable;
	private $m_sIdField;
	
	public function __construct(DbConnection $oConnection, $sTable, $sIdField) {
		$this->m_sConnection = $oConnection->getIdentifier();
		$this->m_oConnection = $oConnection;
		$this->m_sTable = '' . $sTable;
		$this->m_sIdField = '' . $sIdField;
	}

	public function __sleep() {
		return array('m_sConnection', 'm_sTable', 'm_sIdField');
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
	 * Retrieve the internal table-name.
	 * 
	 * @return string
	 */
	public function getTable() {
		return $this->m_sTable;
	}

	/**
	 * Retrieve the internal idField-name.
	 * 
	 * @return string
	 */
	public function getIdField() {
		return $this->m_sIdField;
	}

	/**
	 * Execute a select statement on this table.
	 * If no id-field-overwrite is given, the default one for this table is used.
	 * 
	 * @see DbConnection::select()
	 * @param mixed $mId
	 * @param string $sIdFieldOverwrite If not null, use this as IdField.
	 * @return PDOStatement
	 */
	public function select($mId, $sIdFieldOverwrite = null) {
		return $this->m_oConnection->select($this->m_sTable, $mId, $sIdFieldOverwrite ? ''.$sIdFieldOverwrite : $this->m_sIdField);
	}
	
	/**
	 * Insert a given set of values in this table.
	 * If no id-field-overwrite is given, the default one for this table is used.
	 * 
	 * @see DbConnection::insert()
	 * @param array $aValues
	 * @param string $sIdFieldOverwrite If not null, use this as IdField.
	 * @return mixed|int The value of the IdField (if it was given as an insert value), or the last-insert-id.
	 */
	public function insert(array $aValues, $sIdFieldOverwrite = null) {
		$sIdField = $sIdFieldOverwrite ? ''.$sIdFieldOverwrite : $this->m_sIdField;
		$nReturn = $this->m_oConnection->insert($this->m_sTable, $aValues);
		return isset($aValues[$sIdField]) ? $aValues[$sIdField] : $nReturn;
	}
	
	/**
	 * Update a given set of values for a identifieable row.
	 * If no id-field-overwrite is given, the default one for this table is used.
	 * 
	 * @see DbConnection::update()
	 * @param array $aValues
	 * @param mixed $mId
	 * @param string $sIdFieldOverwrite If not null, use this as IdField.
	 * @return boolean
	 */
	public function update(array $aValues, $mId, $sIdFieldOverwrite = null) {
		return $this->m_oConnection->update($this->m_sTable, $aValues, $mId, $sIdFieldOverwrite ? ''.$sIdFieldOverwrite : $this->m_sIdField);
	}
	
	/**
	 * Delete a identifieable row from the table.
	 * If no id-field-overwrite is given, the default one for this table is used.
	 * 
	 * @see DbConnection::delete()
	 * @param mixed $mId
	 * @param string $sIdFieldOverwrite If not null, use this as IdField.
	 * @return boolean
	 */
	public function delete($mId, $sIdFieldOverwrite = null) {
		return $this->m_oConnection->delete($this->m_sTable, $mId, $sIdFieldOverwrite ? ''.$sIdFieldOverwrite : $this->m_sIdField);
	}
}
