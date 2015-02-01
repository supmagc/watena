<?php
/**
 * This class is meant to represent a table row.
 * Each row should be uniquely identifiable by a set of values from multiple columns.
 * If required you can inherit and add additional logic to handle the internal data.
 *
 * @author Jelle
 * @version 0.3.1
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
		// Don't to anything if considered deleted
		if($this->m_bDeleted) {
			return false;
		}
		
		// Make sure the field exists in the data and can be compared
		if(!isset($this->m_aData[$sColumn]) && !array_key_exists($sColumn, $this->m_aData)) {
			$this->m_bDeleted = true;
			return false;
		}
			
		// Only update if suspected database value is different
		if($this->m_aData[$sColumn] == $mValue) {
			return true;
		}
			
		// If the update fails, flag as deleted
		if(!$this->getTable()->update(array($sColumn => $mValue), array_values($this->m_aIds))) {
			$this->m_bDeleted = true;
			return false;
		}

		// Update cached values if changed in database
		if(in_array($sColumn, $this->m_oTable->getIdFields())) {
			$sIdOld = self::generateUniqueKey($this->m_oTable, $this->m_aIds);
			$this->m_aIds[$sColumn] = $mValue;
			$sIdNew = self::generateUniqueKey($this->m_oTable, $this->m_aIds);
			self::updateUniqueInstance($sIdOld, $sIdNew);
		}
		$this->m_aData[$sColumn] = $mValue;
		return true;
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
		$this->getTable()->delete(array_values($this->m_aIds));
	}
	
	/**
	 * Check if the data of this instance/row is supposed to be deleted.
	 * 
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->m_bDeleted;
	}
	
	/**
	 * Load an object for the given Id.
	 * If an equivalent object allready exists, the function will check if it is deleted.
	 * 
	 * 
	 * !! This class no longer supports is_array($mData) since it allowed to easily for
	 * objects with incomplete row-data (such as default values).
	 * 
	 * @param DbMultiTable $oTable
	 * @param array $aIds
	 * @param array $aIdFieldsOverwrite
	 * @return ObjectUnique|null Will return null when unable to load designated object.
	 */
	public static final function loadObject(DbMultiTable $oTable, array $aIds, array $aIdFieldsOverwrite = array()) {
		$sKey = $oTable->getConnection()->getIdentifier() .'.'. $oTable->getTable() .'.'. implode('.', $oTable->getIdFields());
		
		$sKey .= '.'.implode('.', $aIds);
		$oInstance = static::getUniqueInstance($sKey);
		if(!$oInstance || $oInstance->m_bDeleted) {
			$oStatement = $oTable->select($aIds, $aIdFieldsOverwrite);
			if($oStatement->rowCount() > 0) {
				$oInstance = $oInstance ?: static::assureUniqueInstance($sKey, array($oTable, $oStatement->fetch(PDO::FETCH_ASSOC)));
				$oInstance->m_bDeleted = false;
			}
		}
		
		return $oInstance;
	}
	
	/**
	 * Create a new object by first inserting the given data, and by calling loadObject next.
	 * loadObject will be called with the Ids returned form the insert query.
	 * 
	 * @see loadObject()
	 * @param DbMultiTable $oTable
	 * @param array $aData
	 * @return ObjectUnique|null
	 */
	public static final function createObject(DbMultiTable $oTable, array $aData) {
		$aIds = $oTable->insert($aData);
		if(is_array($aIds) && count($aIds) == count($oTable->getIdFields())) {
			return static::loadObject($oTable, $aIds);
		}
		else {
			return null;
		}
	}
	
	/**
	 * Generate the unique key for the given parameters.
	 * 
	 * @param DbTable $oTable
	 * @param array $aIds
	 * @return string
	 */
	public static final function generateUniqueKey(DbMultiTable $oTable, array $aIds) {
		return $oTable->getConnection()->getIdentifier() .'.'. $oTable->getTable() .'.'. implode('.', $oTable->getIdFields()) .'.'. implode('.', $aIds);
	}
}
