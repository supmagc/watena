<?php

class DbObject extends Object {

	private $m_aData;
	private $m_oTable;
	private $m_bDeleted;
	
	private static $s_aObjectInstances = array();
	
	protected final function __construct(DbTable $oTable, $mData) {
		parent::__construct();
		$this->m_oTable = $oTable;
		if(is_array($mData)) {
			$this->m_aData = $mData;
		}
		else if(is_numeric($mData)) {
			$oStatement = $this->getTable()->select((int)$mData);
			if(($aRow = $oStatement->fetch(PDO::FETCH_ASSOC)) !== false) {
				$this->m_aData = $aRow;
			}
		}
		
		if($this->m_aData === null)
			throw new DbInvalidDbObjectData($this->getTable(), $mData);
		else
			self::$s_aObjectInstances[get_class($this)][$this->getId()] = $this;
	}
	
	protected function getDataValue($sColumn) {
		return isset($this->m_aData[$sColumn]) ? $this->m_aData[$sColumn] : false;
	}
	
	protected function setDataValue($sColumn, $mValue) {
		if($this->getTable()->update($this->getId(), array($sColumn => $mValue))) {
			$this->m_aData[$sColumn] = $mValue;
		}
	}
	
	public function getTable() {
		return $this->m_oTable;
	}
	
	public function getId() {
		return $this->m_aData[$this->getTable()->getIdField()];
	}
	
	public function delete() {
		unset(self::$s_aObjectInstances[get_class($this)][$this->getId()]);
		$this->getTable()->delete($this->getId());
		$this->m_bDeleted = true;
	}
	
	public function isDeleted() {
		return (bool)$this->m_bDeleted;
	}
	
	public static final function loadObject($sClass, DbTable $oTable, $mData) {
		$nId = 0;
		if($sClass == get_class() || !class_exists($sClass) || !is_a($sClass, get_class()))
			return false;
		if(!isset(self::$s_aObjectInstances[$sClass]))
			self::$s_aObjectInstances[$sClass] = array();
		if(is_array($mData) && isset($mData[$oTable->getIdField()]))
			$nId = (int)$mData[$oTable->getIdField()];
		else if(is_numeric($mData))
			$nId = (int)$mData;
		return isset(self::$s_aObjectInstances[$sClass][$nId]) ? self::$s_aObjectInstances[$sClass][$nId] : new $sClass($oTable, $mData);
	}
	
	public static final function createObject($sClass, DbTable $oTable, array $aData) {
		$nId = $oTable->insert($aData);
		return self::loadObject($sClass, $oTable, $nId);
	}
}

?>