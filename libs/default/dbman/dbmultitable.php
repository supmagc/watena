<?php

class DbMultiTable {

	private $m_sConnection;
	private $m_oConnection;
	private $m_sTable;
	private $m_aIdFields;
	private $m_sConcatenation;
	
	public function __construct(DbConnection $oConnection, $sTable, array $aIdFields, $sConcatenation) {
		$this->m_oConnection = $oConnection;
		$this->m_sTable = $sTable;
		$this->m_sIdFields = $aIdFields;
 		$this->m_sConcatenation = $sConcatenation;
	}

	public function __sleep() {
		return array('m_sConnection', 'm_sTable', 'm_aIdFields', 'm_sConcatenation');
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
	 * Retrieve the internal array with id-field-names.
	 * 
	 * @return array
	 */
	public function getIdFields() {
		return $this->m_aIdFields;
	}
	
	/**
	 * Retrieve the internal concatenation-string
	 * 
	 * @return string
	 */
	public function getConcatenation() {
		return $this->m_sConcatenation;
	}

	/**
	 * Check if the given id is valid for this table's id-field
	 * 
	 * @param mixed $mId
	 * @return bool
	 */
	public function isValidId(array $aIds) {
		return count($this->getIdFields()) == count($aIds);
	}

	public function select(array $aIds = array()) {
		return $this->getConnection()->select($this->getTable(), $aIds, $this->getIdFields(), $this->getConcatenation());
	}
	
	public function insert(array $aValues) {
		$aReturn = array();
		$this->getConnection()->insert($this->getTable(), $aValues);
		foreach($this->getIdFields() as $sField)
			$aReturn []= isset($aValues[$sField]) ? $aValues[$sField] : null;
		return $aReturn;
	}
	
	public function update(array $aValues, array $aIds) {
		return $this->getConnection()->update($this->getTable(), $aValues, $aIds, $this->getIdFields(), $this->getConcatenation());
	}
	
	public function delete(array $aIds) {
		return $this->getConnection()->delete($this->getTable(), $aIds, $this->getIdFields(), $this->getConcatenation());
	}
}

?>