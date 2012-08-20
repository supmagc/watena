<?php

class Mapping extends Object {

	private $m_aVariables = array();
	private $m_sTotal;
	
	private static $s_aDefaults;
	
	public final function __construct($sLocal = null, array $aParams = array()) {
		if(!is_array(self::$s_aDefaults)) {
			self::assure();
			self::$s_aDefaults = array();
			self::$s_aDefaults['host'] = $_SERVER['HTTP_HOST'];
			self::$s_aDefaults['port'] = $_SERVER['SERVER_PORT'];
			self::$s_aDefaults['useragent'] = $_SERVER['HTTP_USER_AGENT'];
			self::$s_aDefaults['offset'] = Encoding::substring($_SERVER['SCRIPT_NAME'], 0, -1 - Encoding::length($_SERVER['SCRIPT_FILENAME']));
			self::$s_aDefaults['method'] = $_SERVER['REQUEST_METHOD'];

			if(isset($_SERVER['REDIRECT_URL'])) {
				self::$s_aDefaults['local'] = Encoding::substring($_SERVER['REDIRECT_URL'], Encoding::length(self::$s_aDefaults['offset']));
			}
			else if(isset($_SERVER['REQUEST_URI'])) {
				$nLength = Encoding::contains($_SERVER['REQUEST_URI'], '?') ? Encoding::indexOf($_SERVER['REQUEST_URI'], '?') - Encoding::length($this->m_aVariables['offset']) : null;
				self::$s_aDefaults['local'] = urldecode(Encoding::substring($_SERVER['REQUEST_URI'], Encoding::length(self::$s_aDefaults['offset']), $nLength));
			}
			else {
				self::$s_aDefaults['local'] = '/';
			}
			
			self::$s_aDefaults['parts'] = explode_trim('/', self::$s_aDefaults['local']);		
			self::$s_aDefaults['root'] = 'http://' . self::$s_aDefaults['host'] . (self::$s_aDefaults['port'] != 80 ? ':'.self::$s_aDefaults['port'] : '') . self::$s_aDefaults['offset'];
			self::$s_aDefaults['full'] = self::$s_aDefaults['root'] . self::$s_aDefaults['local'];
		}
		
		$this->m_aVariables = self::$s_aDefaults;
		
		if($sLocal) {
			if(($nIndex = Encoding::indexOf($sLocal, '?'))) {
				
			}
			
			if(Encoding::beginsWith($sLocal, '/')) {
				$this->m_aVariables['local'] = $sLocal;
				$this->m_aVariables['parts'] = explode_trim('/', $sLocal);		
			}
			else {			
				$aParts = self::$s_aDefaults['parts'];
				array_pop($aParts);
				array_push($aParts, $sLocal);
				$this->m_aVariables['local'] = '/' . implode('/', $aParts);
				$this->m_aVariables['parts'] = $aParts;
			}
		}		
	}
	
	/**
	 * Get a variable linked to one of the defined getters.
	 * 
	 * @param string $sName
	 * @return string
	 */
	public final function getVariable($sName) {
		$sName = Encoding::toLower($sName);
		return isset($this->m_aVariables[$sName]) ? $this->m_aVariables[$sName] : false;
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => subdomain.example.org
	 * 
	 * @return string
	 */
	public final function getHost() {
		return $this->getVariable('host');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => https://subdomain.example.org[:80]
	 * 
	 * @return string
	 */
	public final function getBase() {
		
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => https://subdomain.example.org[:80]/path-to-site
	 * 
	 * @return string
	 */
	public final function getRoot() {
		return $this->getVariable('root');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => 80
	 * 
	 * @return string
	 */
	public final function getPort() {
		return $this->getVariable('port');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => /path-to-site
	 * 
	 * @return string
	 */
	public final function getOffset() {
		return $this->getVariable('offset');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => /directory/script.php
	 * 
	 * @return string
	 */
	public final function getLocal() {
		return $this->getVariable('local');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => 0: directory
	 * => 1: script.php
	 * 
	 * @return string
	 */
	public final function getPart($nIndex) {
		$aParts = $this->getVariable('parts');
		return $nIndex < count($aParts) ? $aParts[$nIndex] : null;
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value
	 * => https://subdomain.example.org[:80]/path-to-site/directory/script.php
	 * 
	 * @return string
	 */
	public final function getFull() {
		return $this->getVariable('full');
	}
	
	public final function getUseragent() {
		return $this->getVariable('useragent');
	}
	
	public final function toString() {
		return $this->getFull();
	}
	
	public final static function host($bInternal = false) {
		if($bInternal && isset($_SERVER['SERVER_NAME'])) return $_SERVER['SERVER_NAME'];
		else if(isset($_SERVER['HTTP_HOST'])) return $_SERVER['HTTP_HOST'];
		else return false;
	}
	
	public final static function base($bInternal = false) {
		$sUrl = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $sUrl .= 's';
		$sUrl .= '://';
		if(self::host($bInternal)) $sUrl .= self::host($bInternal);
		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') $sUrl .= ":".$_SERVER['SERVER_PORT'];
		return $sUrl;
	}
	
	public final static function current($bInternal = false) {
		return self::base($bInternal) . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
	}
	
	public final static function detail($bInternal) {
		$aDetails = array();
		if(isset($_SERVER['REQUEST_METHOD'])) $aDetails[] = 'method:' . $_SERVER['REQUEST_METHOD'];
		if(isset($_SERVER['SERVER_PROTOCOL'])) $aDetails[] = 'protocol:' . $_SERVER['SERVER_PROTOCOL'];
		return self::current($bInternal) . ' [' . implode(', ', $aDetails) . ']';
	}
	
	public final static function assure() {
		if(!isset($_SERVER['HTTP_HOST'])) throw new AssureException("Make sure \$_SERVER['HTTP_HOST'] is set!");
		if(!isset($_SERVER['SERVER_PORT'])) throw new AssureException("Make sure \$_SERVER['SERVER_PORT'] is set!");
		if(!isset($_SERVER['SCRIPT_NAME'])) throw new AssureException("Make sure \$_SERVER['SCRIPT_NAME'] is set!");
		if(!isset($_SERVER['SCRIPT_FILENAME'])) throw new AssureException("Make sure \$_SERVER['SCRIPT_FILENAME'] is set!");
		if(!isset($_SERVER['HTTP_USER_AGENT'])) throw new AssureException("Make sure \$_SERVER['HTTP_USER_AGENT'] is set!");
		if(!isset($_SERVER['REQUEST_METHOD'])) throw new AssureException("Make sure \$_SERVER['REQUEST_METHOD'] is set!");
		return true;
	}
}

?>