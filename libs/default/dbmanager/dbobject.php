<?php
/**
 * This class is meant to represent a table row.
 * If required you can inherit and add additional logic to handle the internal data.
 * 
 * This class locks down the default serialization handles, but supports
 * an optional virtual function to extentd on it's behaviour.
 * @see DbObject::dataRecover()
 * 
 * Multiple instances may exists, but they will be linked to the same row-data
 * when their DbTable and Id matches.
 * If inheriting this class you must respect this by only having relationships to other DbObject data.
 * 
 * @author Jelle
 * @version 0.2.0
 */
class DbObject extends Object {

	private $m_mId;
	private $m_sGroup;
	private $m_aData;
	private $m_oTable;
	private $m_bDeleted;
	
	private static $s_aObjectInstances = array();
	
	/**
	 * Internal constructor for an object representing a table row, identifiablme by a single column.
	 * 
	 * This method must be protected or weaker, since Object's constructor is protected
	 * 
	 * @param DbTable $oTable Table object contraining table name and default ID column.
	 * @param array $aData An array with the row data.
	 */
	protected final function __construct(DbTable $oTable, array $aData) {
		parent::__construct();
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		$this->dataInit(false);
	}

	private final function dataInit($bVerify) {
		// Make sure an ID can be found
		if(!isset($this->m_aData[$this->m_oTable->getIdField()])) {
			throw new DbInvalidDbObjectId($this->m_oTable, null);
		}

		// Get initialy required data
		$this->m_mId = $this->m_aData[$this->m_oTable->getIdField()];
		$this->m_sGroup = $this->m_oTable->getConnection()->getIdentifier() . $this->m_oTable->getTable();

		// Make sure a subarray for the data of this connection-table exists
		if(!isset(self::$s_aData[$this->m_sGroup])) {
			self::$s_aData[$this->m_sGroup] = array();
		}
		
		// Check if data is allready loaded and load or save it to the data array
		if(!isset(self::$s_aData[$this->m_sGroup][$this->m_mId])) { // not yet loaded => save to array
			if($bVerify && !$this->m_bDeleted) { // Only verify when needed, and when not yet deleted
				$oStatement = $this->m_oTable->select($this->m_mId);
				$this->m_bDeleted = $oStatement->rowCount() == 0;
			}
			self::$s_aData[$this->m_sGroup][$this->m_mId] = array();
			self::$s_aData[$this->m_sGroup][$this->m_mId]['deleted'] = &$this->m_bDeleted;
			self::$s_aData[$this->m_sGroup][$this->m_mId]['data'] = &$this->m_aData;
		}
		else { // data found => load from array
			$this->m_bDeleted = &self::$s_aData[$this->m_sGroup][$this->m_mId]['deleted'];
			$this->m_aData = &self::$s_aData[$this->m_sGroup][$this->m_mId]['data'];
		}
		
		$this->dataRecover();
	}
	
	public function dataRecover() {
	}
	
	/**
	 * Only save part of the data when serializing
	 * 
	 * @return array
	 */
	public final function __sleep() {
		return array('m_aData', 'm_oTable', 'm_bDeleted');
	}
	
	/**
	 * Re-init the data structure opun unserialize
	 */
	public final function __wakeup() {
		$this->dataInit(true);
	}

	/**
	 * Verify the data structure on clone
	 */
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
		return (!$this->m_bDeleted && isset($this->m_aData[$sColumn])) ? $this->m_aData[$sColumn] : $mDefault;
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
	 * If an equivalent object allready exists, a new instance is created which is linked to the
	 * earlier data by reference.
	 * Thus you can only compare these objects base don their Id's
	 * 
	 * @param string $sClass
	 * @param DbTable $oTable
	 * @param mixed|array $mData
	 * @return Object|false Will return false when unable to load designated object.
	 */
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