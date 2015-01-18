<?php
/**
 * Serializable database table representation.
 * Each row is identifiable by a set of multiple columns
 * 
 * @author Jelle Voet
 * @version 0.2.1
 *
 */
class DbMultiTable extends ObjectUnique {

	private $m_oConnection;
	private $m_sTable;
	private $m_aIdFields;
	
	protected function init(DbConnection $oConnection, $sTable, array $aIdFields) {
		$this->m_oConnection = $oConnection;
		$this->m_sTable = ''.$sTable;
		$this->m_aIdFields = $aIdFields;
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
	 * Retrieve the internal array with id-field-names.
	 * 
	 * @return array
	 */
	public function getIdFields() {
		return $this->m_aIdFields;
	}
	
	/**
	 * Execute a select statement on this table.
	 * If no id-fields-overwrite are given, the default ones for this table are used.
	 * 
	 * @see DbConnection::select()
	 * @param array $aIds Should be of equal size as IdFields.
	 * @param array $aIdFieldsOverwrite If of equal size as $aIds, use these as IdFields.
	 * @return PDOStatement
	 */
	public function select(array $aIds = array(), array $aIdFieldsOverwrite = array()) {
		return $this->m_oConnection->select($this->m_sTable, $aIds, count($aIds) == count($aIdFieldsOverwrite) ? $aIdFieldsOverwrite : $this->m_aIdFields);
	}
	
	/**
	 * Insert a given set of values in this table.
	 * If no id-fields-overwrite are given, the default ones for this table are used.
	 * 
	 * @see DbConnection::insert()
	 * @param array $aValues
	 * @param array $aIdFieldsOverwrite If of equal size as $aIds, use these as IdFields.
	 * @return false|array|int False if the query failed, the values of IdFields (if they where given as insert values), or the last-insert-id.
	 */
	public function insert(array $aValues, array $aIdFieldsOverwrite = array()) {
		$aReturn = array();
		$aIdFields = count($aIdFieldsOverwrite) > 0 ? $aIdFieldsOverwrite : $this->m_aIdFields;
		$nReturn = $this->m_oConnection->insert($this->m_sTable, $aValues);
		foreach($aIdFields as $sField)
			$aReturn []= isset($aValues[$sField]) ? $aValues[$sField] : null;
		return count($aReturn) == count($aIdFields) ? $aReturn : $nReturn;
	}
	
	/**
	 * Update a given set of values for a identifiable row.
	 * If no id-fields-overwrite are given, the default ones for this table are used.
	 * 
	 * @see DbConnection::update()
	 * @param array $aValues
	 * @param array $aIds Should be of equal size as IdFields.
	 * @param array $aIdFieldsOverwrite If of equal size as $aIds, use these as IdFields.
	 * @return false|int False if the query failed, or the number of affected rows.
	 */
	public function update(array $aValues, array $aIds, array $aIdFieldsOverwrite = array()) {
		return $this->m_oConnection->update($this->m_sTable, $aValues, $aIds, count($aIds) == count($aIdFieldsOverwrite) ? $aIdFieldsOverwrite : $this->m_aIdFields);
	}
	
	/**
	 * Delete a identifiable row from the table.
	 * If no id-fields-overwrite are given, the default ones for this table are used.
	 * 
	 * @see DbConnection::delete()
	 * @param array $aIds Should be of equal size as IdFields.
	 * @param array $aIdFieldsOverwrite If of equal size as $aIds, use these as IdFields.
	 * @return false|int False if the query failed, or the number of affected rows.
	 */
	public function delete(array $aIds, array $aIdFieldsOverwrite = array()) {
		return $this->m_oConnection->delete($this->m_sTable, $aIds, count($aIds) == count($aIdFieldsOverwrite) ? $aIdFieldsOverwrite : $this->m_aIdFields);
	}
	
	/**
	 * Assure the existance of a single unique DbTable instance.
	 * 
	 * @see DbMultiTable::init()
	 * @see ObjectUnisue::assureUniqueInstance()
	 * @param DbConnection $oConnection
	 * @param string $sTable
	 * @param array $aIdFields
	 * @return DbMultiTable
	 */
	public static final function assureUniqueDbMultiTable(DbConnection $oConnection, $sTable, array $aIdFields) {
		return self::assureUniqueInstance($oConnection->getIdentifier() . $sTable . implode('-', $aIdFields), array($oConnection, $sTable, $aIdFields));
	}
}
