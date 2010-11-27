<?php

class Mapping extends Object {

	private $m_aVariables = array();
	
	public function __construct() {
		$this->m_aVariables['host'] = $_SERVER['HTTP_HOST'];
		$this->m_aVariables['root'] = $_SERVER['DOCUMENT_ROOT'];
		$this->m_aVariables['port'] = $_SERVER['SERVER_PORT'];
		$this->m_aVariables['offset'] = Encoding::substring($_SERVER['SCRIPT_NAME'], 0, Encoding::length($_SERVER['SCRIPT_NAME']) - 10);
		$this->m_aVariables['useragent'] = $_SERVER['HTTP_USER_AGENT'];
		if(isset($_SERVER['REDIRECT_URL'])) {
			$this->m_aVariables['mapping'] = Encoding::substring($_SERVER['REDIRECT_URL'], Encoding::length($this->m_aVariables['offset']));
		}
		else {
			$nLength = Encoding::contains($_SERVER['REQUEST_URI'], '?') ? Encoding::indexOf($_SERVER['REQUEST_URI'], '?') - Encoding::length($this->m_aVariables['offset']) : null;
			$this->m_aVariables['mapping'] = urldecode(Encoding::substring($_SERVER['REQUEST_URI'], Encoding::length($this->m_aVariables['offset']), $nLength));
		}
	}
	
	public function getVariable($sName) {
		return $this->m_aVariables[Encoding::stringToLower($sName)];
	}
}

?>