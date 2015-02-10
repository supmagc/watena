<?php
/**
 * This class is meant to represent a table row.
 * Each row should be uniquely identifiable by the unique value within a single column.
 * If required you can inherit and add additional logic to handle the internal data.
 * 
 * @author Jelle
 * @version 0.3.1
 */
class DbObject extends ObjectUnique {

	private $m_mId;
	private $m_aData;
	private $m_oTable;
	private $m_bDeleted;

	/**
	 * Internal callback upon instance creation.
	 * Overwrite this method in child classes to add behaviour.
	 */
	protected function onInit() {}
	
	/**
	 * Internal callback upon deletion.
	 * Overwrite this method in child classes to add behaviour.
	 */
	protected function onDelete() {}
	
	/**
	 * Internal constructor for an object representing a table row, identifiablme by a single column.
	 * 
	 * @throws DbInvalidDbObjectId When the required ID is not found within $aData.
	 * @param DbTable $oTable Table object contraining table name and default ID column.
	 * @param array $aData An array with the row data.
	 */
	public final function init(DbTable $oTable, array $aData) {
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		$this->m_mId = $this->m_aData[$this->m_oTable->getIdField()];
		
		// Make sure an ID can be found
		if(!isset($this->m_aData[$this->m_oTable->getIdField()])) {
			throw new DbInvalidDbObjectId($this->m_oTable);
		}
		
		// Call callback
		$this->onInit();
	}
	
	/**
	 * Get the value for a specific column, or $mDefault if none is set.
	 * Even when deleted, this might still return the old values.
	 * 
	 * @param string $sColumn Column name.
	 * @param mixed $mDefault Default value when no column is found. (default: false, not null, since null is a valid sql-value)
	 * @return mixed|$mDefault Returns $mDefault is when column is not found.
	 */
	protected final function getDataValue($sColumn, $mDefault = false) {
		return (isset($this->m_aData[$sColumn]) || array_key_exists($sColumn, $this->m_aData)) ? $this->m_aData[$sColumn] : $mDefault;
	}

	/**
	 * Try to update the given value in this instance, and
	 * reflect that change on the database-table.
	 * This won't change anything of the instance/row is considered deleted.
	 * 
	 * @see DbTable::update()
	 * @param string $sColumn
	 * @param mixed $mValue
	 * @return boolean
	 */
	protected final function setDataValue($sColumn, $mValue) {
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
		if(!$this->getTable()->update(array($sColumn => $mValue), $this->m_mId)) {
			$this->m_bDeleted = true;
			return false;
		}

		// Update cached values if changed in database
		if($sColumn == $this->m_oTable->getIdField()) {
			$sIdOld = self::generateUniqueKey($this->m_oTable, $this->m_mId);
			$this->m_mId = $mValue;
			$sIdNew = self::generateUniqueKey($this->m_oTable, $this->m_mId);
			self::updateUniqueInstance($sIdOld, $sIdNew);
		}
		$this->m_aData[$sColumn] = $mValue;
		return true;
	}
	
	/**
	 * Get the associated DbTable instance.
	 * 
	 * @return DbTable
	 */
	public final function getTable() {
		return $this->m_oTable;
	}
	
	/**
	 * Get the row ID.
	 * 
	 * @return mixed
	 */
	public final function getId() {
		return $this->m_mId;
	}
	
	/**
	 * Delete this row from the database, and flag this instance as deleted.
	 */
	public final function delete() {
		if($this->m_bDeleted) 
			return;
		
		$this->m_bDeleted = true;
		$this->getTable()->delete($this->m_mId);
		self::setUniqueInstance(self::generateUniqueKey($this->m_oTable, $this->m_mId), null);
		
		// Call callback
		$this->onDelete();
	}
	
	/**
	 * Check if the data of this instance/row is supposed to be deleted.
	 * 
	 * @return boolean
	 */
	public final function isDeleted() {
		return $this->m_bDeleted;
	}
	
	/**
	 * Load an object for the given Id.
	 * If an equivalent object allready exists, the function will check if it is deleted.
	 * 
	 * !! This class no longer supports is_array($mData) since it allowed to easily for
	 * objects with incomplete row-data (such as default values).
	 * 
	 * @param DbTable $oTable
	 * @param mixed $mId
	 * @param string $sIdFieldOverwrite
	 * @return DbObject|null Will return null when unable to load designated object.
	 */
	public static final function loadObject(DbTable $oTable, $mId, $sIdFieldOverwrite = null) {
		$sKey = self::generateUniqueKey($oTable, $mId);
		$oInstance = static::getUniqueInstance($sKey);
		if(!$oInstance || $oInstance->m_bDeleted) {
			$oStatement = $oTable->select($mId, $sIdFieldOverwrite);
			if($oStatement->rowCount() > 0) {
				$oInstance = $oInstance ?: static::assureUniqueInstance($sKey, array($oTable, $oStatement->fetch(PDO::FETCH_ASSOC)));
				$oInstance->m_bDeleted = false;
			}
		}
		
		return $oInstance;
	}
	
	/**
	 * Scan for valid records through the given PDOStatement.
	 * You need to make sure teh rows are full representations of the table data, and have
	 * a valid Id matching $oTable->getIdField().
	 * 
	 * @param DbTable $oTable
	 * @param PDOStatement $oStatement
	 * @throws DbInvalidDbObjectId
	 * @return array<DbObject>
	 */
	public static final function loadObjectList(DbTable $oTable, PDOStatement $oStatement) {
		$aReturn = array();
		foreach($oStatement as $aRow) {
			if(!isset($aRow[$oTable->getIdField()]))
				throw new DbInvalidDbObjectId($oTable);
			
			$mId = $aRow[$oTable->getIdField()];
			$sKey = self::generateUniqueKey($oTable, $mId);
			$aReturn[$mId] = static::assureUniqueInstance($sKey, array($oTable, $aRow));
		}
		return $aReturn;
	} 
	
	/**
	 * Create a new object by first inserting the given data, and by calling loadObject next.
	 * loadObject will be called with the Id given in the data, or if none found, the insert-id
	 * returned from the insert statement.
	 * 
	 * @see loadObject()
	 * @param DbTable $oTable
	 * @param array $aData
	 * @return DbObject|null
	 */
	public static final function createObject(DbTable $oTable, array $aData) {
		$nId = $oTable->insert($aData);
		if(isset($aData[$oTable->getIdField()])) {
			return static::loadObject($oTable, $aData[$oTable->getIdField()]);
		}
		else {
			return static::loadObject($oTable, $nId);
		}
	}
	
	/**
	 * Generate the unique key for the given parameters.
	 * 
	 * @param DbTable $oTable
	 * @param mixed $mId
	 * @return string
	 */
	public static final function generateUniqueKey(DbTable $oTable, $mId) {
		return $oTable->getConnection()->getIdentifier() .'.'. $oTable->getTable() .'.'. $oTable->getIdField() .'.' .$mId;
	}
}
