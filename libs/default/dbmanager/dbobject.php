<?php

class DbObject extends Object {

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
	protected final function __construct(DbTable $oTable, array $aData) {
		parent::__construct();
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		self::$s_aObjectInstances[get_class($this)][$this->getId()] = $this;
	}
	
	/**
	 * Get the value for a specific column, or the default value if none is set.
	 * 
	 * @param string $sColumn Column name.
	 * @param mixed $mDefault Default value when no column is found. (not null, since null is a valid sql-value)
	 * @return mixed|false Returns $mDefault is when column is not found.
	 */
	protected function getDataValue($sColumn, $mDefault = false) {
		return (!$this->m_aData && isset($this->m_aData[$sColumn])) ? $this->m_aData[$sColumn] : $mDefault;
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
	protected function setDataValue($sColumn, $mValue) {
		if(!$this->m_bDeleted && $this->getTable()->update(array($sColumn => $mValue), $this->getId())) {
			$this->m_aData[$sColumn] = $mValue;
			return true;
		}
		else {
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
		return $this->m_aData[$this->getTable()->getIdField()];
	}
	
	/**
	 * Delete this row from the database, and flag this instance as deleted.
	 */
	public function delete() {
		if($this->m_bDeleted) 
			return;
		
		unset(self::$s_aObjectInstances[get_class($this)][$this->getId()]);
		$this->getTable()->delete($this->getId());
		$this->m_bDeleted = true;
	}
	
	/**
	 * Check if this instance is supposed to be deleted.
	 * 
	 * @return boolean
	 */
	public function isDeleted() {
		return (bool)$this->m_bDeleted;
	}
	
	public static final function loadObject($sClass, DbTable $oTable, $mData) {
		if($sClass == get_class() || !class_exists($sClass) || !is_subclass_of($sClass, get_class()))
			return false;
		if(!isset(self::$s_aObjectInstances[$sClass]))
			self::$s_aObjectInstances[$sClass] = array();
		if(is_array($mData) && isset($mData[$oTable->getIdField()])) {
			$sKey = $mData[$oTable->getIdField()];
			return isset(self::$s_aObjectInstances[$sClass][$sKey]) ? self::$s_aObjectInstances[$sClass][$sKey] : new $sClass($oTable, $mData);
		}
		else if(!is_array($mData)) {
			if(isset(self::$s_aObjectInstances[$sClass][$mData])) {
				return self::$s_aObjectInstances[$sClass][$mData];
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
		$nId = $oTable->insert($aData);
		return self::loadObject($sClass, $oTable, $nId);
	}
}

?>