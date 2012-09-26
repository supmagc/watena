<?php

class Mapping extends Object {

	private $m_aVariables = array();
	private $m_aParams = array();
	private $m_sAnchor = null;
	
	private static $s_aDefaults;
	
	/**
	 * Create a new mapping instance.
	 * You can specify the following types of local-mapping:
	 * absolute: (starting with a /) A mapping relative to the root.
	 * relative: (not starting with a /) A mapping relative to the directory of the current mapping.
	 * current: (current mapping) Specify null (default-value).
	 * 
	 * @param string $sLocal The local mapping.
	 * @param array $aParams Additional GET-parameters.
	 */
	public final function __construct($sLocal = null, array $aParams = array()) {
		if(!is_array(self::$s_aDefaults)) {
			$this->getLogger()->error('Mapping not yet inited!');
		}

		// Load defaults and params
		$this->m_aVariables = self::$s_aDefaults;
		$this->m_aParams = $aParams;
		
		// Parse localpart
		if($sLocal) {
			if(($nIndex = Encoding::indexOf($sLocal, '#'))) {
				$this->m_aVariables['anchor'] = Encoding::substring($sLocal, $nIndex + 1);
				$sLocal = Encoding::substring($sLocal, 0, $nIndex);
			}			
			
			if(($nIndex = Encoding::indexOf($sLocal, '?'))) {
				parse_str(Encoding::substring($sLocal, $nIndex + 1), $aParams);
				$this->m_aParams = array_merge($this->m_aParams, $aParams);
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

		// Combine query and full url
		$this->m_aVariables['full'] = $this->m_aVariables['root'] . $this->m_aVariables['local'];		
		if(count($this->m_aParams) > 0) {
			$this->m_aVariables['query'] = http_build_query($this->m_aParams, null, '&');
			$this->m_aVariables['full'] .= '?' . $this->m_aVariables['query'];
		}
		if($this->m_aVariables['anchor'] !== null) {
			$this->m_aVariables['full'] .= '#' . $this->m_aVariables['anchor'];
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
		return isset($this->m_aVariables[$sName]) ? $this->m_aVariables[$sName] : null;
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => subdomain.example.org
	 * 
	 * @return string
	 */
	public final function getHost() {
		return $this->getVariable('host');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => https://subdomain.example.org[:80]
	 * 
	 * @return string
	 */
	public final function getBase() {
		return $this->getVariable('host');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => 80
	 * 
	 * @return string
	 */
	public final function getPort() {
		return $this->getVariable('port');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => /path-to-site
	 * 
	 * @return string
	 */
	public final function getOffset() {
		return $this->getVariable('offset');
	}
	
	/**
	* example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	* => https://subdomain.example.org[:80]/path-to-site
	*
	* @return string
	*/
	public final function getRoot() {
		return $this->getVariable('root');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => /directory/script.php
	 * 
	 * @return string
	 */
	public final function getLocal() {
		return $this->getVariable('local');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => 0: directory
	 * => 1: script.php
	 * => 2: null
	 * 
	 * @return string|null
	 */
	public final function getPart($nIndex) {
		$aParts = $this->getVariable('parts');
		return $nIndex < count($aParts) ? $aParts[$nIndex] : null;
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => name=value
	 *
	 * @return string|null
	 */
	public final function getQuery() {
		return $this->getVariable('query');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => name: value
	 * => unknown: null
	 *
	 * @return string|null
	 */
	public final function getParam($sName) {
		return isset($this->m_aParams[$sName]) ? $this->m_aParams[$sName] : null;
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => anchor
	 *
	 * @return string|null
	 */
	public final function getAnchor() {
		return $this->getVariable('anchor');
	}
	
	/**
	 * example: https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * => https://subdomain.example.org[:80]/path-to-site/directory/script.php?name=value#anchor
	 * 
	 * @return string
	 */
	public final function getFull() {
		return $this->getVariable('full');
	}
	
	public final function getUseragent() {
		return $this->getVariable('useragent');
	}

	/**
	 * Returns the full url of this instance
	 * 
	 * @see Mapping::getFull()
	 * @return string
	 */
	public final function toString() {
		return $this->getFull();
	}

	/**
	 * Retrieve the hostname of the current request.
	 * Optionally you can try to retrieve the actual server-name as specified on the server.
	 * If not requested, the value returned will the host-portion of the request is specified.
	 * If none of these options are viable the return value will be 'unknown'.
	 * 
	 * @return string
	 */
	public final static function host($bInternal = false) {
		if(($bInternal || empty($_SERVER['HTTP_HOST'])) && !empty($_SERVER['SERVER_NAME'])) {
			return $_SERVER['SERVER_NAME'];
		}
		else if(!empty($_SERVER['HTTP_HOST'])) {
			return $_SERVER['HTTP_HOST'];
		}
		else {
			return 'unknown';
		}
	}

	/**
	 * Retrieve the portnumber of the current request.
	 * If no port is specified, this will default to 80.
	 * 
	 * @return int
	 */
	public final static function port() {
		return !empty($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
	}

	/**
	 * Retrieve the request-method of the current request.
	 * If no request-method is specified, this will default to GET.
	 * 
	 * @return string
	 */
	public final static function method() {
		return !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET'; 
	}

	/**
	 * Retrieve the useragent of the current request.
	 * This will automatically save the useragent to a session.
	 * If a subsequent request with useragent * / * should occur, the session-value will be used instead.
	 * If no useragent is specified, this will default to 'Unknown'.
	 * 
	 * @return string
	 */ 
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
	
	/**
	 * Retrieve the offset of the current request.
	 * The offset is the mapping from the host to the root (index.php and .htaccess) of the framework.
	 * 
	 * @return string
	 */
	public final static function offset() {
		return !empty($_SERVER['DOCUMENT_ROOT']) ? Encoding::replace('\\', '/', Encoding::substring(PATH_ROOT, Encoding::length($_SERVER['DOCUMENT_ROOT']))) : '';
	}

	/**
	 * Initialize the mapping by calculating the current request values.
	 * This must be called before any instance creation of this class.
	 */
	public final static function init() {
		self::$s_aDefaults = array();
		self::$s_aDefaults['query'] = null;
		self::$s_aDefaults['anchor'] = null;
		self::$s_aDefaults['host'] = self::host();
		self::$s_aDefaults['port'] = self::port();
		self::$s_aDefaults['method'] = self::method();
		self::$s_aDefaults['useragent'] = self::userAgent();
		self::$s_aDefaults['offset'] = self::offset();
		
		if(isset($_SERVER['REDIRECT_URL'])) {
			self::$s_aDefaults['local'] = Encoding::substring($_SERVER['REDIRECT_URL'], Encoding::length(self::$s_aDefaults['offset']));
		}
		else if(isset($_SERVER['REQUEST_URI'])) {
			$nLengthOffset = Encoding::length($this->m_aVariables['offset']);
			$nLengthLocal = Encoding::contains($_SERVER['REQUEST_URI'], '?') ? (Encoding::indexOf($_SERVER['REQUEST_URI'], '?') - $nLengthOffset) : null;
			self::$s_aDefaults['local'] = urldecode(Encoding::substring($_SERVER['REQUEST_URI'], $nLengthOffset, $nLengthLocal));
		}
		else {
			self::$s_aDefaults['local'] = '/';
		}
		
		self::$s_aDefaults['root'] = 'http://' . self::$s_aDefaults['host'] . (self::$s_aDefaults['port'] != 80 ? ':'.self::$s_aDefaults['port'] : '') . self::$s_aDefaults['offset'];
		self::$s_aDefaults['parts'] = explode_trim('/', self::$s_aDefaults['local']);
	}
}

?>
