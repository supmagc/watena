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
		'url' => 'http://localhost',
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
		
		$sBuilder = '[' . self::$s_aData['method'] . ']' . $sBuilder;
		self::$s_aData['detail'] = $sBuilder;
	}

	/**
	 * Determine if the current request uses a secured protocol.
	 * The return value is purely based on the value of $_SERVER['HTTPS']
	 * 
	 * @return boolean
	 */
	public final static function isHttp() {
		return self::$s_aData['http'];
	}
	
	public final static function isHttps() {
		return self::$s_aData['https'];
	}
	
	/**
	 * Retrieve the scheme/protocol of the current request.
	 * Currently the only two protocols supported are 'http' and 'https'.
	 * The return value of this method is determined from Request::isHttps().
	 * 
	 * @see Request::isHttps()
	 * @return string Return value should be 'http' or 'https'.
	 */
	public final static function protocol() {
		return self::$s_aData['protocol'];
	} 
	
	/**
	 * Retrieve the hostname of the current request is lowercase.
	 * Optionally you can try to retrieve the actual server-name as specified on the server config.
	 * If not requested, the value returned will be the host-portion of the request.
	 * The return value will be based on $_SERVER['SERVER_NAME'] or $_SERVER['HTTP_POST'].
	 * Should these two variables not be available, the return value will be 'localhost'.
	 *
	 * @return string
	 */
	public final static function host($bServer = false) {
		return $bServer ? self::$s_aData['host_server'] : self::$s_aData['host_http'];
	}
	
	/**
	 * Retrieve the portnumber as integer of the current request.
	 * If no port is specified, this will default to 80 for http and 443 for https.
	 *
	 * @return int
	 */
	public final static function port() {
		return self::$s_aData['port'];
	}
	
	/**
	 * 
	 * @return string
	 */
	public final static function base() {
		return self::$s_aData['base'];
	}

	/**
	 * Retrieve the offset of the current request.
	 * The offset is the mapping from the host to the root (index.php and .htaccess) of the framework.
	 *
	 * @return string
	 */
	public final static function offset() {
		return self::$s_aData['offset'];
	}
	
	public final static function root() {
		return self::$s_aData['root'];
	}
	
	public final static function path() {
		return self::$s_aData['path'];
	}
	
	public final static function url() {
		return self::$s_aData['url'];
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
}

?>