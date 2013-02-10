<?php

class Request {

	/**
	 * Determine if the current request uses a secured protocol.
	 * The return value is purely based on the value of $_SERVER['HTTPS']
	 * 
	 * @return boolean
	 */
	public final static function isHttps() {
		return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
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
		return self::isHttps() ? 'https' : 'http';
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
	public final static function host($bInternal = false) {
		if(($bInternal || empty($_SERVER['HTTP_HOST'])) && !empty($_SERVER['SERVER_NAME'])) {
			return Encoding::toLower($_SERVER['SERVER_NAME']);
		}
		else if(!empty($_SERVER['HTTP_HOST'])) {
			return Encoding::toLower($_SERVER['HTTP_HOST']);
		}
		else {
			return 'localhost';
		}
	}
	
	/**
	 * Retrieve the portnumber as integer of the current request.
	 * If no port is specified, this will default to 80 for http and 443 for https.
	 *
	 * @return int
	 */
	public final static function port() {
		return !empty($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : (self::isHttps() ? 443 : 80);
	}
	
	/**
	 * 
	 * @return string
	 */
	public final static function base() {
		$sBase = self::protocol() . '://' . self::host();
		if(self::isHttps()) {
			if(self::port() != 443)
				$sBase .= ':' . self::port();
		}
		else {
			if(self::port() != 80)
				$sBase .= ':' . self::port();
		}
		return $sBase;
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
	
	public final static function root() {
		return self::base() . self::offset();
	}
	
	public final static function path() {
		$sPath = '';
		if(isset($_SERVER['REDIRECT_URL'])) {
			$sPath = $_SERVER['REDIRECT_URL'];
		}
		else if(isset($_SERVER['PHP_SELF'])) {
			$sPath = $_SERVER['PHP_SELF'];
		}
		return Encoding::substring($sPath, Encoding::length(self::offset()));
	}
	
	public final static function url() {
		return self::root() . self::path();
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
			return 'watena';
		}
	}
}

?>