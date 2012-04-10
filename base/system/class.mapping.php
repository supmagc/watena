<?php

class Mapping extends Object {

	private $m_aVariables = array();
	private $m_sTotal;
	
	public final function __construct($sLocal = null) {
		$this->m_aVariables['host'] = $_SERVER['HTTP_HOST'];
		//$this->m_aVariables['root'] = $_SERVER['DOCUMENT_ROOT'];
		$this->m_aVariables['port'] = $_SERVER['SERVER_PORT'];
		$this->m_aVariables['offset'] = Encoding::substring($_SERVER['SCRIPT_NAME'], 0, Encoding::length($_SERVER['SCRIPT_NAME']) - 10);
		$this->m_aVariables['useragent'] = $_SERVER['HTTP_USER_AGENT'];
		if($sLocal !== null) {
			$this->m_aVariables['local'] = $sLocal;		
		}
		else if(isset($_SERVER['REDIRECT_URL'])) {
			$this->m_aVariables['local'] = Encoding::substring($_SERVER['REDIRECT_URL'], Encoding::length($this->m_aVariables['offset']));
		}
		else {
			$nLength = Encoding::contains($_SERVER['REQUEST_URI'], '?') ? Encoding::indexOf($_SERVER['REQUEST_URI'], '?') - Encoding::length($this->m_aVariables['offset']) : null;
			$this->m_aVariables['local'] = urldecode(Encoding::substring($_SERVER['REQUEST_URI'], Encoding::length($this->m_aVariables['offset']), $nLength));
		}
		
		// Save main as it's used a lot
		$this->m_aVariables['root'] = 'http://' . $this->m_aVariables['host'] . ($this->m_aVariables['port'] != 80 ? ':'.$this->m_aVariables['port'] : '') . $this->m_aVariables['offset'];
		$this->m_aVariables['full'] =  $this->m_aVariables['root'] . $this->m_aVariables['local'];
	}
	
	public final function getVariable($sName) {
		$sName = Encoding::toLower($sName);
		return isset($this->m_aVariables[$sName]) ? $this->m_aVariables[$sName] : false;
	}
	
	public final function getHost() {
		return $this->getVariable('host');
	}
	
	public final function getRoot() {
		return $this->getVariable('root');
	}
	
	public final function getPort() {
		return $this->getVariable('port');
	}
	
	public final function getOffset() {
		return $this->getVariable('offset');
	}
	
	public final function getLocal() {
		return $this->getVariable('local');
	}
	
	public final function getFull() {
		return $this->getVariable('full');
	}
	
	public final function getUseragent() {
		return $this->getVariable('useragent');
	}
	
	public final function toString() {
		return $this->getFull();
	}
}

?>