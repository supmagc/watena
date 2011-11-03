<?php

class DbConnection {
	
	private $m_sHost;
	private $m_sUser;
	private $m_sPass;
	private $m_sName;
	private $m_nPort;
	private $m_sType;
	
	private $m_oConnection;
	
	public function __construct($sHost, $sUser, $sPass, $sName, $nPort, $sType) {
		
	}
	
	public function __sleep() {
		return array('m_sHost', 'm_sUser', 'm_sPass', 'm_sName', 'm_nPort', 'm_sType');
	}
	
	public function __wakeup() {
		
	}
}

?>