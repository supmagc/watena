<?php

class DbConnection {
	
	private $m_sDsn;
	private $m_sUser;
	private $m_sPass;
	
	private $m_oConnection;
	
	public function __construct($sDsn, $sUser, $sPass) {
		$this->m_sDsn = $sDsn;
		$this->m_sUser = $sUser;
		$this->m_sPass = $sPass;
		$this->connect();
	}
	
	public function __sleep() {
		return array('m_sDsn', 'm_sUser', 'm_sPass');
	}
	
	public function __wakeup() {
		$this->connect();
	}
	
	public function getDsn() {
		return $this->m_sDsn;
	}
	
	public function getUser() {
		return $this->m_sUser;
	}
	
	public function getPass() {
		return $this->m_sPass;
	}
	
	/**
	 * Retrieve the underlying PDO-Object
	 * 
	 * @return PDO
	 */
	public function getPdo() {
		return $this->m_oConnection;
	}
	
	public function connect() {
		if($this->m_oConnection === null) {
			$this->m_oConnection = new PDO($this->getDsn(), $this->getUser(), $this->getPass(), array(PDO::ATTR_PERSISTENT => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
			$this->m_oConnection->query('SET time_zone = \'+00:00\';'); // Set to UTC
			$this->m_oConnection->query('SET wait_timeout = 120;');
		}
	}
	
	public function disconnect() {
		if($this->m_oConnection !== null)
			$this->m_oConnection = null;
	}
	
	public function getTable($sTable, $mIdField = 'ID') {
		return new DbTable($this, $sTable, $mIdField);
	}
	
	public function getMultiTable($sTable, array $aIdFields = array('ID'), $sConcatenation = 'AND') {
		return new DbMultiTable($this, $sTable, $aIdFields, $sConcatenation);
	}
	
	public function query($sQuery, array $aParams = array()) {
		$oStatement = $this->getPdo()->prepare($sQuery);
		$oStatement->execute($aParams);
		return $oStatement;
	}
	
	public function call($sName, array $aParams, array $aReturns) {
		$sPartA = implode(', ', array_fill(0, count($aParams), '?'));
		$sPartB = implode(', ', array_map(create_function('$a', 'return "@$a";'), $aReturns));
		$sPartC = implode(', ', array_map(create_function('$a', 'return "@$a AS `$a`";'), $aReturns));
		$sQuery = "CALL `$sName`(".$sPartA.(Encoding::Length($sPartA) > 0 && Encoding::Length($sPartB) > 0 ? ', ' : '').$sPartB.")";
		$oStatement = System::PDO()->prepare($sQuery);
		$oStatement->execute($aParams);
		return $this->getPdo()->query("SELECT $sPartC");
	}
	
	public function select($sTable, $mId = null, $mIdField = 'ID', $sConcatenation = 'AND') {
		if($mId !== null || (is_array($mId) && count($mId) > 0)) {
			list($sWhere, $aWheres) = $this->buildWhere($mId, $mIdField, $sConcatenation);
			$sQuery = "SELECT * FROM `$sTable` WHERE $sWhere";
			$oStatement = $this->getPdo()->prepare($sQuery);
			$oStatement->execute($aWheres);
			return $oStatement;
		}
		else {
			$sQuery = "SELECT * FROM `$sTable`";
			$oStatement = $this->getPdo()->prepare($sQuery);
			$oStatement->execute();
			return $oStatement;
		}
	}
	
	public function insert($sTable, array $aData, $bTransaction = true) {
		$mId = false;
		$aFields = array_keys($aData);
		$sFields = implode(', ', array_map(create_function('$a', 'return "`$a`";'), $aFields));
		$sValues = implode(', ', array_map(create_function('$a', 'return ":$a";'), $aFields));
		$sQuery = 'INSERT INTO `'.$sTable.'` ('.$sFields.') VALUES ('.$sValues.')';
		if($bTransaction) $this->getPdo()->beginTransaction();
		try {
			$oStatement = $this->getPdo()->prepare($sQuery);
			$oStatement->execute($aData);
			$mId = $this->getPdo()->lastInsertId();
		}
		catch(PDOException $e) {
			if($bTransaction) $this->getPdo()->rollBack();
			throw $e;
		}
		if($bTransaction) $this->getPdo()->commit();
		return $mId;
	}
	
	public function update($sTable, array $aData, $mId, $mIdField = 'ID', $sConcatenation = 'AND') {
		list($sWhere, $aWheres) = $this->buildWhere($mId, $mIdField, $sConcatenation);
		$sUpdates = implode(', ', array_map(create_function('$a', 'return "`$a` = :$a";'), array_keys($aData)));
		$sQuery = "UPDATE `$sTable` SET ".$sUpdates." WHERE $sWhere";
		$oStatement = $this->getPdo()->prepare($sQuery);
		return $oStatement->execute(array_merge($aData, $aWheres));
	}
	
	public function delete($sTable, $mId, $mIdField = 'ID', $sConcatenation = 'AND') {
		list($sWhere, $aWheres) = $this->buildWhere($mId, $mIdField, $sConcatenation);
		$sQuery = "DELETE FROM `$sTable` WHERE $sWhere";
		$oStatement = $this->getPdo()->prepare($sQuery);
		return $oStatement->execute($aWheres);
	}

	public function buildWhere($mId, $mIdField, $sConcatenation = 'AND') {
		if(!is_array($mId)) $mId = array($mId);
		if(!is_array($mIdField)) $mIdField = array($mIdField);
		$bAssoc = array_assoc($mId);
		$aWheres = array_map(create_function('$a', 'return "`$a` = :$a";'), $bAssoc ? array_keys($mId) : $mIdField);
		return array(implode(" $sConcatenation ", $aWheres), $bAssoc ? $mId : array_combine($mIdField, $mId));
	}
}

?>