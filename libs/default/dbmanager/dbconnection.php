<?php

class DbConnection {
	
	private $m_sDsn;
	private $m_sUser;
	private $m_sPass;
	
	private $m_oConnection;
	
	/**
	 * Create a new database-connection.
	 * This internally creates a PDO instance
	 * 
	 * @param string $sDsn as required for a PDO connection
	 * @param string $sUser
	 * @param string $sPass
	 */
	public function __construct($sDsn, $sUser, $sPass) {
		$this->m_sDsn = $sDsn;
		$this->m_sUser = $sUser;
		$this->m_sPass = $sPass;
		$this->connect();
	}
	
	/**
	 * Don't save the actual PDO instance to the session storage.
	 * 
	 * @return array
	 */
	public function __sleep() {
		return array('m_sDsn', 'm_sUser', 'm_sPass');
	}
	
	/**
	 * Restore the PDO connection when retrieving this instance from session storage.
	 */
	public function __wakeup() {
		$this->connect();
	}

	/**
	 * Get the data-source-name as used to create tthe PDO instance.
	 * 
	 * @return string
	 */
	public function getDsn() {
		return $this->m_sDsn;
	}
	
	/**
	 * Get the connection username.
	 * 
	 * @return string
	 */
	public function getUser() {
		return $this->m_sUser;
	}
	
	/**
	 * Get the connection password.
	 * 
	 * @return string
	 */
	public function getPass() {
		return $this->m_sPass;
	}
	
	/**
	 * Retrieve the underlying PDO-instance
	 * If the connection is not yet established, thiw will retuen null.
	 * 
	 * @see connect()
	 * @return PDO|null
	 */
	public function getPdo() {
		return $this->m_oConnection;
	}
	
	/**
	 * Make the actual connection.
	 * This will initialize the PDO instance.
	 * 
	 * @see getPdo()
	 */
	public function connect() {
		if($this->m_oConnection === null) {
			$this->m_oConnection = new PDO($this->getDsn(), $this->getUser(), $this->getPass(), array(
				PDO::ATTR_PERSISTENT => false, 
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
			$this->m_oConnection->query('SET names '.Encoding::replace('-', '', Encoding::charset()).';'); // Set to UTC
			$this->m_oConnection->query('SET time_zone = \''.date('P').'\';'); // Set to UTC
			$this->m_oConnection->query('SET wait_timeout = 120;');
		}
	}
	
	/**
	 * Terminate the PDO sonnection.
	 */
	public function disconnect() {
		if($this->m_oConnection !== null)
			$this->m_oConnection = null;
	}
	
	/**
	 * Get a table instance for which every row is identified by a single deterministic ID field.
	 * 
	 * @param string $sTable
	 * @param string $mIdField
	 * @return DbTable
	 */
	public function getTable($sTable, $mIdField = 'ID') {
		return new DbTable($this, $sTable, $mIdField);
	}
	
	/**
	 * Get a table instance for which every row is identified by a deterministic set of ID fields.
	 * 
	 * @param string $sTable
	 * @param array $aIdFields
	 * @param string $sConcatenation
	 * @return DbMultiTable
	 */
	public function getMultiTable($sTable, array $aIdFields = array('ID'), $sConcatenation = 'AND') {
		return new DbMultiTable($this, $sTable, $aIdFields, $sConcatenation);
	}
	
	/**
	 * Execute a custom query with some optional arguments.
	 * 
	 * @param string $sQuery
	 * @param array $aParams
	 * @return PDOStatement
	 */
	public function query($sQuery, array $aParams = array()) {
		$oStatement = $this->m_oConnection->prepare($sQuery);
		$oStatement->execute($aParams);
		return $oStatement;
	}
	
	/**
	 * Call a custom mysql routine with additional parameters and return values.
	 * 
	 * @param string $sName
	 * @param array $aParams
	 * @param array $aReturns
	 * @return PDOStatement
	 */	
	public function call($sName, array $aParams, array $aReturns) {
		$sPartA = implode(', ', array_fill(0, count($aParams), '?'));
		$sPartB = implode(', ', array_map(create_function('$a', 'return "@$a";'), $aReturns));
		$sPartC = implode(', ', array_map(create_function('$a', 'return "@$a AS `$a`";'), $aReturns));
		$sQuery = "CALL `$sName`(".$sPartA.(Encoding::Length($sPartA) > 0 && Encoding::Length($sPartB) > 0 ? ', ' : '').$sPartB.")";
		$oStatement = $this->m_oConnection->prepare($sQuery);
		$oStatement->execute($aParams);
		return $this->m_oConnection->query("SELECT $sPartC");
	}

	/**
	 * Select a rowfrom a specific table identified by a single or an array of ID field(s).
	 * The variables $mId and $mIdField can be mixed. For more information see buildWhere()
	 * 
	 * @see buildWhere()
	 * @param string $sTable
	 * @param mixed $mId
	 * @param mixed $mIdField
	 * @param string $sConcatenation
	 * @return PDOStatement
	 */
	public function select($sTable, $mId = null, $mIdField = 'ID', $sConcatenation = 'AND') {
		if($mId !== null || (is_array($mId) && count($mId) > 0)) {
			list($sWhere, $aWheres) = $this->buildWhere($mId, $mIdField, $sConcatenation);
			$sQuery = "SELECT * FROM `$sTable` WHERE $sWhere";
			$oStatement = $this->m_oConnection->prepare($sQuery);
			$oStatement->execute($aWheres);
			return $oStatement;
		}
		else {
			$sQuery = "SELECT * FROM `$sTable`";
			$oStatement = $this->m_oConnection->prepare($sQuery);
			$oStatement->execute();
			return $oStatement;
		}
	}

	/**
	 * Insert a record in a specific table.
	 * This function works transactional and rolls back since it automatically
	 * request the last-insert-id. (which is a seperate SQL query)
	 * 
	 * @param string $sTable
	 * @param array $aData
	 * @param boolean $bTransaction
	 * @throws PDOException
	 * @return mixed
	 */
	public function insert($sTable, array $aData, $bTransaction = true) {
		$mId = false;
		$aFields = array_keys($aData);
		$sFields = implode(', ', array_map(create_function('$a', 'return "`$a`";'), $aFields));
		$sValues = implode(', ', array_map(create_function('$a', 'return ":$a";'), $aFields));
		$sQuery = 'INSERT INTO `'.$sTable.'` ('.$sFields.') VALUES ('.$sValues.')';
		if($bTransaction) $this->m_oConnection->beginTransaction();
		try {
			$oStatement = $this->m_oConnection->prepare($sQuery);
			$oStatement->execute($aData);
			$mId = $this->getPdo()->lastInsertId();
		}
		catch(PDOException $e) {
			if($bTransaction) $this->m_oConnection->rollBack();
			throw $e;
		}
		if($bTransaction) $this->m_oConnection->commit();
		return $mId;
	}
	
	/**
	 * Update a row from a specific table identified by a single or an array of ID field(s).
	 * The variables $mId and $mIdField can be mixed. For more information see buildWhere()
	 * 
	 * @see buildWhere()
	 * @param string $sTable
	 * @param array $aData
	 * @param mixed $mId
	 * @param mixed $mIdField
	 * @param string $sConcatenation
	 * @return boolean
	 */
	public function update($sTable, array $aData, $mId, $mIdField = 'ID', $sConcatenation = 'AND') {
		list($sWhere, $aWheres) = $this->buildWhere($mId, $mIdField, $sConcatenation);
		$sUpdates = implode(', ', array_map(create_function('$a', 'return "`$a` = :$a";'), array_keys($aData)));
		$sQuery = "UPDATE `$sTable` SET ".$sUpdates." WHERE $sWhere";
		$oStatement = $this->m_oConnection->prepare($sQuery);
		return $oStatement->execute(array_merge($aData, $aWheres));
	}
	
	/**
	 * Update a row from a specific table identified by a single or an array of ID field(s).
	 * The variables $mId and $mIdField can be mixed. For more information see buildWhere()
	 * 
	 * @see buildWhere()
	 * @param string $sTable
	 * @param mixed $mId
	 * @param mixed $mIdField
	 * @param string $sConcatenation
	 * @return boolean
	 */
	public function delete($sTable, $mId, $mIdField = 'ID', $sConcatenation = 'AND') {
		list($sWhere, $aWheres) = $this->buildWhere($mId, $mIdField, $sConcatenation);
		$sQuery = "DELETE FROM `$sTable` WHERE $sWhere";
		$oStatement = $this->m_oConnection->prepare($sQuery);
		return $oStatement->execute($aWheres);
	}

	/**
	 * This method builds the select/where part of a query in a format that supports PDO query-variables.
	 * The building is semi smart for binary comparisons ans 'null statements.
	 * 
	 * You can specify a single value for $mId and $mIdField, or you can
	 * specify array values with equel count.
	 * 
	 * If the value in Id is null the comparison will become 'IS' or 'IS NOT'
	 * 
	 * You can specify the following binary directives for IdField:
	 * !: Becomes <> in the query
	 * <: Becomes < in the query
	 * >: Becomes > in the query
	 * =: Becomes = in the query
	 * 
	 * @param mixed $mId
	 * @param mixed $mIdField
	 * @param string $sConcatenation
	 * @return list(string, array)
	 */
	public function buildWhere($mId, $mIdField, $sConcatenation = 'AND') {
		if(!is_array($mId)) $mId = array($mId);
		if(!is_array($mIdField)) $mIdField = array($mIdField);
		$aWheres = array();
		$aIdFieldCount = array();
		for($i=0 ; $i<count($mIdField) ; ++$i) {
			$aMatches = array();
			$aIdFieldCount []= "var$i";
			Encoding::regFind('^(.*?)([!<>=]?)(.*?)$',  $mIdField[$i], $aMatches);
			$sField = $aMatches[1] . $aMatches[3];
			$sCompare = $aMatches[2];
			if($mId[$i] === null) $sCompare = $sCompare == '!' ? "IS NOT" : "IS";
			else if($sCompare == '!') $sCompare ='<>';
			else if(!$sCompare == '!') $sCompare ='=';
			$aWheres []=  "`$sField` " . $sCompare . " :var$i";
		}
		return array(implode(" $sConcatenation ", $aWheres), array_combine($aIdFieldCount, $mId));
	}
}

?>