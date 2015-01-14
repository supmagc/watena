<?php
/**
 * This class is meant to represent a table row.
 * If required you can inherit and add additional logic to handle the internal data.
 * 
 * @author Jelle
 * @version 0.2.2
 */
class DbObject extends ObjectUnique {

	private $m_mId;
	private $m_aData;
	private $m_oTable;
	private $m_bDeleted;
	
	private static $s_aObjectInstances = array();
	
	/**
	 * Internal constructor for an object representing a table row, identifiablme by a single column.
	 * 
	 * @param DbTable $oTable Table object contraining table name and default ID column.
	 * @param array $aData An array with the row data.
	 */
	public final function init(DbTable $oTable, array $aData) {
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		$this->m_mId = $this->m_aData[$this->m_oTable->getIdField()];
		
			// Make sure an ID can be found
		if(!isset($this->m_aData[$this->m_oTable->getIdField()])) {
			throw new DbInvalidDbObjectId($this->m_oTable, null);
		}
	}
	
	/**
	 * Get the value for a specific column, or the default value if none is set.
	 * 
	 * @param string $sColumn Column name.
	 * @param mixed $mDefault Default value when no column is found. (default: false, not null, since null is a valid sql-value)
	 * @return mixed|$mDefault Returns $mDefault is when column is not found.
	 */
	protected final function getDataValue($sColumn, $mDefault = false) {
		return (!$this->m_bDeleted && (isset($this->m_aData[$sColumn]) || array_key_exists($sColumn, $this->m_aData))) ? $this->m_aData[$sColumn] : $mDefault;
	}

	/**
	 * Try to update the given value in this instance, and
	 * reflect that change on the database-table.
	 * 
	 * @see DbTable::update()
	 * @param string $sColumn
	 * @param mixed $mValue
	 * @return boolean
	 */
	protected final function setDataValue($sColumn, $mValue) {
		if(!$this->m_bDeleted && $this->getTable()->update(array($sColumn => $mValue), $this->m_mId)) {
			if($sColumn == $this->m_oTable->getIdField()) {
				$this->m_mId = $mValue;
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
	 * Get the DbTable instance.
	 * 
	 * @return DbTable
	 */
	public function getTable() {
		return $this->m_oTable;
	}
	
	/**
	 * Get the row ID.
	 * 
	 * @return mixed
	 */
	public function getId() {
		return $this->m_mId;
	}
	
	/**
	 * Delete this row from the database, and flag this instance as deleted.
	 */
	public function delete() {
		if($this->m_bDeleted) 
			return;
		
		$this->m_bDeleted = true;
		$this->getTable()->delete($this->m_mId);
	}
	
	/**
	 * Check if this instance is supposed to be deleted.
	 * 
	 * @return boolean
	 */
	public function isDeleted() {
		return $this->m_bDeleted;
	}
	
	/**
	 * Load an object for the given Id.
	 * If an equivalent object allready exists, the same instance will be returned
	 * 
	 * !! This class no longer supports is_array($mData) since it allowed to easily for
	 * objects with incomplete row-data (such as default values).
	 * 
	 * @param DbTable $oTable
	 * @param mixed $mData
	 * @param string $sIdFieldOverwrite
	 * @return Object|null Will return null when unable to load designated object.
	 */
	public static final function loadObject(DbTable $oTable, $mData, $sIdFieldOverwrite = null) {
		$sKey = $oTable->getConnection()->getIdentifier() . $oTable->getTable() . $oTable->getIdField();
		
		if(!is_array($mData)) {
			$sKey .= $mData;
			$oInstance = static::getUniqueInstance($sKey);
			if($oInstance) {
				return $oInstance;
			}
			else {
				$oStatement = $oTable->select($mData, $sIdFieldOverwrite);
				if($oStatement->rowCount() > 0) {
					return static::assureUniqueInstance($sKey, array($oTable, $oStatement->fetch(PDO::FETCH_ASSOC)));
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Create a new object by first inserting the given data, and by calling loadObject next.
	 * loadObject will be called with the Id given in the data, or if none found, the insert-id
	 * returned from the insert statement.
	 * 
	 * @see loadObject()
	 * @param DbTable $oTable
	 * @param array $aData
	 * @return Object|null
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
}
