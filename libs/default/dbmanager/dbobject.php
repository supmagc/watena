<?php
/**
 * This class is meant to represent a table row.
 * If required you can inherit and add additional logic to handle the internal data.
 * 
 * This class locks down the default serialization handles, but supports
 * optional virtual functions to extentd on it's behaviour.
 * 
 * @author Jelle
 * @version 0.2.0
 */
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
	private final function __construct(DbTable $oTable, array $aData) {
		parent::__construct();
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		$this->dataInit(false);
	}

	private final function dataInit($bVerify) {
		
		self::$s_aObjectInstances[get_class($this)][$this->getId()] = $this;
	}
	
	public final function __sleep() {
		return array('m_aData', 'm_oTable', 'm_bDeleted');
	}
	
	public final function __wakeup() {
		$this->dataInit(true);
	}
	
	public final function __clone() {
		$this->dataInit(false);
	}
	
	/**
	 * Get the value for a specific column, or the default value if none is set.
	 * 
	 * @param string $sColumn Column name.
	 * @param mixed $mDefault Default value when no column is found. (not null, since null is a valid sql-value)
	 * @return mixed|false Returns $mDefault is when column is not found.
	 */
	protected final function getDataValue($sColumn, $mDefault = false) {
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
	protected final function setDataValue($sColumn, $mValue) {
		if(!$this->m_bDeleted && $this->getTable()->update(array($sColumn => $mValue), $this->getId())) {
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