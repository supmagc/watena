<?php

class DbObject extends Object {

	private $m_aData;
	private $m_oTable;
	private $m_bDeleted;
	
	private static $s_aObjectInstances = array();
	
	protected final function __construct(DbTable $oTable, array $aData) {
		parent::__construct();
		$this->m_oTable = $oTable;
		$this->m_aData = $aData;
		self::$s_aObjectInstances[get_class($this)][$this->getId()] = $this;
	}
	
	protected function getDataValue($sColumn, $mDefault = false) {
		return isset($this->m_aData[$sColumn]) ? $this->m_aData[$sColumn] : $mDefault;
	}
	
	protected function setDataValue($sColumn, $mValue) {
		if($this->getTable()->update(array($sColumn => $mValue), $this->getId())) {
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
		if($sClass == get_class() || !class_exists($sClass) || !is_a($sClass, get_class()))
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