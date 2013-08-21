<?php

class Request {
	
	private static $s_aData = array(
		'useragent' => 'watena',
		'http' => true,
		'https' => false,
		'protocol' => 'http',
		'user' => '',
		'password' => '',
		'host_http' => 'localhost',
		'host_server' => 'localhost',
		'port' => 80,
		'offset' => '',
		'path' => '/',
		'method' => 'GET',
		'mapping' => array(),
		'base' => 'http://localhost',
		'root' => 'http://localhost',
		'url' => 'http://localhost/',
		'detail' => '[GET] http://localhost',
	);
	
	public final static function init() {
		if(!empty($_SERVER['HTTP_USER_AGENT'])) {
			if(empty($_SESSION['HTP_USER_AGENT'])) {
				self::$s_aData['useragent'] = $_SESSION['HTTP_USER_AGENT'];
			}
			else {
				self::$s_aData['useragent'] = $_SERVER['HTTP_USER_AGENT'];
			}
			if($_SERVER['HTTP_USER_AGENT'] !== '*/*') {
				$_SESSION['HTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			}
		}
		
		if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			self::$s_aData['http'] = false;
			self::$s_aData['https'] = true;
			self::$s_aData['protocol'] = 'https';
			self::$s_aData['port'] = 443;
		}
		
		if(!empty($_SERVER['HTTP_HOST'])) {
			self::$s_aData['host_http'] = $_SERVER['HTTP_HOST'];
			if(empty( $_SERVER['SERVER_NAME']))
				self::$s_aData['host_server'] = $_SERVER['HTTP_HOST'];
		}
		
		if(!empty($_SERVER['SERVER_NAME'])) {
			self::$s_aData['host_server'] = $_SERVER['SERVER_NAME'];
			if(empty( $_SERVER['HTTP_HOST']))
				self::$s_aData['host_server'] = $_SERVER['SERVER_NAME'];
		}
		
		if(!empty($_SERVER['SERVER_PORT'])) {
			self::$s_aData['port'] = $_SERVER['SERVER_PORT'];
		}
		
		if(!empty($_SERVER['DOCUMENT_ROOT'])) {
			self::$s_aData['offset'] = Encoding::replace('\\', '/', Encoding::substring(PATH_ROOT, Encoding::length($_SERVER['DOCUMENT_ROOT'])));
		}

		$sPath = '';
		if(isset($_SERVER['REDIRECT_URL'])) {
			$sPath = $_SERVER['REDIRECT_URL'];
		}
		else if(isset($_SERVER['REQUEST_URI'])) {
			$sPath = Encoding::substring($_SERVER['REQUEST_URI'], 0, Encoding::indexOf($_SERVER['REQUEST_URI'], '?'));
		}
		else if(isset($_SERVER['PHP_SELF'])) {
			$sPath = $_SERVER['PHP_SELF'];
		}
		self::$s_aData['path'] = Encoding::substring($sPath, Encoding::length(self::offset()));
		self::$s_aData['mapping'] = explode_trim('/', self::$s_aData['path']);
		
		if(!empty($_SERVER['REQUEST_METHOD'])) {
			self::$s_aData['method'] = Encoding::toUpper($_SERVER['REQUEST_METHOD']);
		}
		
		$sBuilder = self::$s_aData['protocol'] . '://' . self::$s_aData['host_http'];
		if(self::$s_aData['https']) {
			if(self::$s_aData['port'] != 443)
				$sBuilder .= ':' . self::$s_aData['port'];
		}
		else {
			if(self::$s_aData['port'] != 80)
				$sBuilder .= ':' . self::$s_aData['port'];
		}
		self::$s_aData['base'] = $sBuilder;
		
		$sBuilder .= self::$s_aData['offset'];
		self::$s_aData['root'] = $sBuilder;
		
		$sBuilder .= self::$s_aData['path'];
		self::$s_aData['url'] = $sBuilder;
		
		$sBuilder = '[' . self::$s_aData['method'] . ']' . $sBuilder . '?' . http_build_query($_GET, null, '&');
		self::$s_aData['detail'] = $sBuilder;
	}

	/**
	 * Determine if the current request is not using a secured protocol.
	 * This method is the opposite of Request::isHttps().
	 * The return value is based on $_SERVER['HTTPS'] and will influence
	 * the behaviour of Request::protocol() and Request::port().
	 * 
	 * @see Request::isHttps()
	 * @see Request::protocol()
	 * @see Request::port()
	 * @return boolean (default: true)
	 */
	public final static function isHttp() {
		return self::$s_aData['http'];
	}
	
	/**
	 * Determine if the current request is using a secured protocol.
	 * This method is the opposite of Request::isHttp().
	 * The return value is based on $_SERVER['HTTPS'] and will influence
	 * the behaviour of Request::protocol() and Request::port().
	 * 
	 * @see Request::isHttps()
	 * @see Request::protocol()
	 * @see Request::port()
	 * @return boolean (default: false)
	 */
	public final static function isHttps() {
		return self::$s_aData['https'];
	}
	
	/**
	 * Retrieve the scheme/protocol of the current request.
	 * Currently the only two protocols supported are 'http' and 'https'.
	 * The return value is based on Request::isHttp() and Request::isHttps().
	 * 
	 * @see Request::isHttp()
	 * @see Request::isHttps()
	 * @return string Return value should be 'http' or 'https'. (default: 'http')
	 */
	public final static function protocol() {
		return self::$s_aData['protocol'];
	} 
	
	public final static function user() {
		return self::$s_aData['user'];
	}

	public final static function password() {
		return self::$s_aData['password'];
	}
	
	/**
	 * Retrieve the lowercase hostname of the current request.
	 * Optionally you can try to retrieve the actual server-name as 
	 * specified on the server config. If not required, the value returned 
	 * will be the host-portion of the request.
	 * The return value is based on $_SERVER['SERVER_NAME'] or $_SERVER['HTTP_POST'].
	 *
	 * @return string Returns lowercase $_SERVER[HTTP_HOST], $_SERVER[SERVER_NAME]. (default: localhost)
	 */
	public final static function host($bServer = false) {
		return $bServer ? self::$s_aData['host_server'] : self::$s_aData['host_http'];
	}
	
	/**
	 * Retrieve the portnumber of the current request.
	 * The return value is based on $_SERVER[SERVER_PORT].
	 * If no port is specified, this will default to 80 for http and 443 for https.
	 *
	 * @see Request::isHttp()
	 * @see Request::isHttps()
	 * @return int The requests port-number. (default based on protocol: 80 or 443)
	 */
	public final static function port() {
		return self::$s_aData['port'];
	}
	
	/**
	 * Retrieve the base-portion of the current request.
	 * 
	 * @see Request::protocol()
	 * @see Request::host()
	 * @see Request::port()
	 * @example http://[user[:pass]@]example.com[:80]
	 * @return string
	 */
	public final static function base() {
		return self::$s_aData['base'];
	}

	/**
	 * Retrieve the offset of the current request.
	 * The offset is defined as the difference between the base and the root url
	 * for the current request and is not fixed 'per install'.
	 *
	 * @example /path-to-install
	 * @return string
	 */
	public final static function offset() {
		return self::$s_aData['offset'];
	}

	/**
	 * Retrieve the root portion of the current request.
	 * 
	 * @return multitype:string boolean number multitype:
	 */
	public final static function root() {
		return self::$s_aData['root'];
	}
	
	public final static function path() {
		return self::$s_aData['path'];
	}
	
	public final static function url() {
		return self::$s_aData['url'];
	}
	
	/**
	 * Retrieve the request-method of the current request.
	 * If no request-method is specified, this will default to GET.
	 *
	 * @return string
	 */
	public final static function method() {
		return self::$s_aData['method'];
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
		return self::$s_aData['useragent'];
	}
	
	public final static function get($mIndex, $mDefault = null) {
		return array_value($_GET, $mIndex, $mDefault);
	} 

	public final static function post($mIndex, $mDefault = null) {
		return array_value($_POST, $mIndex, $mDefault);
	}

	public final static function session($mIndex, $mDefault = null) {
		return array_value($_SESSION, $mIndex, $mDefault);
	}

	public final static function cookie($mIndex, $mDefault = null) {
		return array_value($_COOKIE, $mIndex, $mDefault);
	}
}

?>