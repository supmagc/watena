<?php
/**
 * This class is meant to represent a table row.
 * Each row should be uniquely identifiable by a set of values from multiple columns.
 * If required you can inherit and add additional logic to handle the internal data.
 *
 * @author Jelle
 * @version 0.2.2
 */
class DbMultiObject extends ObjectUnique {

	private $m_aIds;
	private $m_aData;
	private $m_oTable;
	private $m_bDeleted;
	
	/**
	 * Internal constructor for an object representing a table row, identifiablme by multiple columns.
	 * 
	 * @throws DbInvalidDbMultiObjectId When the required ID's are not found within $aData.
	 * @param DbMultiTable $oTable Table object contraining table name and default ID columns.
	 * @param array $aData An array with the row data.
	 */
	protected final function init(DbMultiTable $oTable, array $aData) {
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		$this->m_aIds = array();
		
		foreach($oTable->getIdFields() as $sIdField) {
			if(isset($this->m_aData[$sIdField])) {
				$this->m_aIds[$sIdField] = $this->m_aData[$sIdField];
			}
			else {
				throw new DbInvalidDbMultiObjectId($oTable, $sIdField);
			}
		}
	}
	
	/**
	 * Get the value for a specific column, or $mDefault if none is set.
	 * Even when deleted, this might still return the old values.
	 * 
	 * @param string $sColumn Column name.
	 * @param mixed $mDefault Default value when no column is found. (default: false, not null, since null is a valid sql-value)
	 * @return mixed|$mDefault Returns $mDefault is when column is not found.
	 */
	protected function getDataValue($sColumn, $mDefault = false) {
		return (isset($this->m_aData[$sColumn]) || array_key_exists($sColumn, $this->m_aData)) ? $this->m_aData[$sColumn] : $mDefault;
	}
	
	/**
	 * Try to update the given value in this instance, and
	 * reflect that change on the database-table.
	 * This won't change anything of the instance/row is considered deleted.
	 * 
	 * @see DbMultiTable::update()
	 * @param string $sColumn
	 * @param mixed $mValue
	 * @return boolean
	 */
	protected function setDataValue($sColumn, $mValue) {
		if(!$this->m_bDeleted && $this->getTable()->update(array($sColumn => $mValue), $this->m_aIds)) {
			if(in_array($sColumn, $this->m_oTable->getIdFields())) {
				$this->m_aIds[$sColumn] = $mValue;
			}
			$this->m_aData[$sColumn] = $mValue;
			return true;
		}
		else {
			$this->delete();
			return false;
		}
	}
	
	/**
	 * Get the associated DbTable instance.
	 * 
	 * @return DbMultiTable
	 */
	public function getTable() {
		return $this->m_oTable;
	}
	
	/**
	 * Get the row IDs.
	 * 
	 * @return array
	 */
	public function getIds() {
		return $this->m_aIds;
	}
	
	/**
	 * Delete this row from the database, and flag this instance as deleted.
	 */
	public function delete() {
		if($this->m_bDeleted)
			return;
		
		$this->m_bDeleted = true;
		$this->getTable()->delete($this->m_aIds);
	}
	
	/**
	 * Check if the data of this instance/row is supposed to be deleted.
	 * 
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->m_bDeleted;
	}
	
	public static final function loadObject($sClass, DbMultiTable $oTable, array $mData) {
		$nId = 0;
		if($sClass == get_class() || !class_exists($sClass) || !is_subclass_of($sClass, get_class()))
			return false;
		if(!isset(self::$s_aObjectInstances[$sClass]))
			self::$s_aObjectInstances[$sClass] = array();
		if(is_assoc($mData)) {
			$aKeys = array();
			foreach($oTable->getIdFields() as $sField) {
				if(!isset($mData[$sField])) return false;
				$aKeys []= $sField . '=' . $mData[$sField];
			}
			$sKey = implode('|', $aKeys);
			return isset(self::$s_aObjectInstances[$sClass][$sKey]) ? self::$s_aObjectInstances[$sClass][$sKey] : new $sClass($oTable, $mData);
		}
		else if($oTable->isValidId($mData)) {			
			$aKeys = array();
			$aFields = $oTable->getIdFields();
			for($i=0 ; $i<count($aFields) ; ++$i) {
				$aKeys []= $aFields[$i] . '=' . $aIds[$i];
			}
			$sKey = implode('|', $aKeys);
			if(isset(self::$s_aObjectInstances[$sClass][$sKey])) {
				return self::$s_aObjectInstances[$sClass][$sKey];
			}
			else {
				$oStatement = $oTable->select($mData);
				return $oStatement->rowCount() > 0 ? new $sClass($oTable, $oStatement->fetch(PDO::FETCH_ASSOC)) : false;
			}
		}
		else {
			return false;
		}
	}
	
	public static final function createObject($sClass, DbTable $oTable, array $aData) {
		$aIds = $oTable->insert($aData);
		return self::loadObject($sClass, $oTable, $aIds);
	}
}
