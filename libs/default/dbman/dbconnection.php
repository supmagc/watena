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
	 * 
	 * Enter description here ...
	 * 
	 * @return PDO
	 */
	public function getPdo() {
		return $this->m_oConnection;
	}
	
	public function connect() {
		if($this->m_oConnection === null)
			$this->m_oConnection = new PDO($this->getDsn(), $this->getUser(), $this->getPass());
	}
	
	public function disconnect() {
		if($this->m_oConnection !== null)
			$this->m_oConnection = null;
	}
	
	public function query() {
		
	}
	
	public function call() {
		
	}
	
	public function select() {
		
	}
	
	public function insert($sTable, array $aValues) {
		$sQuery = 'INSERT INTO `' . $sTable . '` () VALUES ()';
		$oStatement = $this->getPdo()->prepare($sQuery);
		$oStatement->execute(array(
		));
	}
	
	public function update($sTable, array $aValues, $sWhere) {
		
	}
	
	public function delete($sTable, $sWhere) {
		
	}	
}

?>