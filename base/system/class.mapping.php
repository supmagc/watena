<?php

class Mapping extends Object {

	private $m_aVariables = array();
	private $m_aParams = array();
	private $m_sAnchor = null;
	
	private static $s_aDefaults;
	
	public final function __construct($sLocal = null, array $aParams = array()) {
		if(!is_array(self::$s_aDefaults)) {
			self::$s_aDefaults = array();
			self::$s_aDefaults['host'] = self::host();
			self::$s_aDefaults['port'] = self::port();
			self::$s_aDefaults['method'] = self::method();
			self::$s_aDefaults['useragent'] = self::userAgent();
			self::$s_aDefaults['offset'] = self::offset();
				
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
				
			self::$s_aDefaults['root'] = 'http://' . self::$s_aDefaults['host'] . (self::$s_aDefaults['port'] != 80 ? ':'.self::$s_aDefaults['port'] : '') . self::$s_aDefaults['offset'];
			self::$s_aDefaults['parts'] = explode_trim('/', self::$s_aDefaults['local']);		
		}
		
		$this->m_aVariables = self::$s_aDefaults;
		
		if($sLocal) {
			if(($nIndex = Encoding::indexOf($sLocal, '#'))) {
				$this->m_sAnchor = Encoding::substring($sLocal, $nIndex + 1);
				$sLocal = Encoding::substring($sLocal, 0, $nIndex);
			}			
			
			if(($nIndex = Encoding::indexOf($sLocal, '?'))) {
				parse_str(Encoding::substring($sLocal, $nIndex + 1), $this->m_aParams);
				$sLocal = Encoding::substring($sLocal, 0, $nIndex);
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
				$this->m_aVariables['parts'] = explode_trim('/', $this->m_aVariables['local']);
			}
		}
		
		$this->m_aVariables['full'] = $this->m_aVariables['root'] . $this->m_aVariables['local'];
		
		if(count($this->m_aParams) > 0) {
			$this->m_aVariables['full'] .= '?' . http_build_query($this->m_aParams, null, '&');
		}
		
		if($this->m_sAnchor !== null) {
			$this->m_aVariables['full'] .= '#' . $this->m_sAnchor;
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
		return $this->getVariable('host');
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
	* => https://subdomain.example.org[:80]/path-to-site
	*
	* @return string
	*/
	public final function getRoot() {
		return $this->getVariable('root');
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
	
	public final function getAnchor() {
		return $this->getVariable('anchor');
	}
	
	public final function getQuery() {
		return $this->getVariable('query');
	}
	
	public final function getParam($sName) {
		return isset($this->m_aParams) ? $this->m_aParams[$sName] : null;
	}
	
	public final function getUseragent() {
		return $this->getVariable('useragent');
	}
	
	public final function toString() {
		return $this->getFull();
	}
	
	public final static function host($bInternal = false) {
		if($bInternal && !empty($_SERVER['SERVER_NAME'])) {
			return $_SERVER['SERVER_NAME'];
		}
		else if(!empty($_SERVER['HTTP_HOST'])) {
			return $_SERVER['HTTP_HOST'];
		}
		else {
			return 'unknown';
		}
	}
	
	public final static function port() {
		return !empty($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
	}
	
	public final static function method() {
		return !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET'; 
	}
	
	public final static function userAgent() {
		if(!empty($_SERVER['HTTP_USER_AGENT'])) {
			if($_SERVER['HTTP_USER_AGENT'] !== '*/*') {
				$_SESSION['HTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			}
			else if(isset($_SESSION['HTTP_USER_AGENT'])) {
				return $_SESSION['HTTP_USER_AGENT'];
			}
			return $_SERVER['HTTP_USER_AGENT'];
		}
		else {
			return 'Unknown';
		}
	}
	
	public final static function offset() {
		return !empty($_SERVER['DOCUMENT_ROOT']) ? Encoding::replace('\\', '/', Encoding::substring(PATH_ROOT, Encoding::length($_SERVER['DOCUMENT_ROOT']))) : '';
	}
}

?>
