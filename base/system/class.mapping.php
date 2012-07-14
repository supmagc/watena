<?php

class Mapping extends Object {

	private $m_aVariables = array();
	private $m_sTotal;
	
	public final function __construct($sLocal = null) {
		$this->m_aVariables['host'] = $_SERVER['HTTP_HOST'];
		$this->m_aVariables['port'] = $_SERVER['SERVER_PORT'];
		$this->m_aVariables['offset'] = Encoding::substring($_SERVER['SCRIPT_NAME'], 0, Encoding::length($_SERVER['SCRIPT_NAME']) - 10);
		$this->m_aVariables['useragent'] = $_SERVER['HTTP_USER_AGENT'];
		if($sLocal !== null && Encoding::beginsWith($sLocal, '/')) {
			$this->m_aVariables['local'] = $sLocal;
		}
		else {
			if(isset($_SERVER['REDIRECT_URL'])) {
				$this->m_aVariables['local'] = Encoding::substring($_SERVER['REDIRECT_URL'], Encoding::length($this->m_aVariables['offset']));
			}
			else {
				$nLength = Encoding::contains($_SERVER['REQUEST_URI'], '?') ? Encoding::indexOf($_SERVER['REQUEST_URI'], '?') - Encoding::length($this->m_aVariables['offset']) : null;
				$this->m_aVariables['local'] = urldecode(Encoding::substring($_SERVER['REQUEST_URI'], Encoding::length($this->m_aVariables['offset']), $nLength));
			}
			
			if($sLocal !== null) {
				$aParts = explode('/', $this->getWatena()->getMapping()->getLocal());
				array_pop($aParts);
				array_push($aParts, $sLocal);
				$this->m_aVariables['local'] = implode('/', $aParts);
			}
		}
		// SPlit the localpart
		$this->m_aVariables['parts'] = explode_trim('/', $this->m_aVariables['local']);
		
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
	
	public final function getPart($nIndex) {
		$aParts = $this->getVariable('parts');
		return $nIndex < count($aParts) ? $aParts[$nIndex] : false;
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
	
	public final static function current() {
		$sUrl = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $sUrl .= 's';
		$sUrl .= '://';
		if(isset($_SERVER['SERVER_NAME'])) $sUrl .= $_SERVER['SERVER_NAME'];
		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') $sUrl .= ":".$_SERVER['SERVER_PORT'];
		if(isset($_SERVER['REQUEST_URI'])) $sUrl .= $_SERVER['REQUEST_URI'];
		if(isset($_SERVER['REQUEST_METHOD'])) $sUrl .= ' (' . $_SERVER['REQUEST_METHOD'] . ')';
		return $sUrl;
	}
	
	public final static function assure() {
		if(!isset($_SERVER['HTTP_HOST'])) throw new AssureException("Make sure \$_SERVER['HTTP_HOST'] is set!");
		if(!isset($_SERVER['SERVER_PORT'])) throw new AssureException("Make sure \$_SERVER['SERVER_PORT'] is set!");
		if(!isset($_SERVER['SCRIPT_NAME'])) throw new AssureException("Make sure \$_SERVER['SCRIPT_NAME'] is set!");
		if(!isset($_SERVER['HTTP_USER_AGENT'])) throw new AssureException("Make sure \$_SERVER['HTTP_USER_AGENT'] is set!");
		if(!isset($_SERVER['REDIRECT_URL']) && !isset($_SERVER['REQUEST_URI'])) throw new AssureException("Make sure \$_SERVER['REDIRECT_URL'] or \$_SERVER['REQUEST_URI'] are set!");
		return true;
	}
}

?>