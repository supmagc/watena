<?php namespace Watena\Core;

class Cookies {
	
	private static $s_sDomain;
	private static $s_sPath;
	
	public static function init($sDomain, $sPath) {
		self::$s_sDomain = '.'.$sDomain;
		self::$s_sPath = $sPath;
	}
	
	public static function save($sName, $mValue, $nDuration = 0, $sPath = '/', $bSecure = false) {
		$bSecure = $bSecure && Request::isHttps();
		if(!headers_sent() && setcookie($sName, ''.$mValue, time() + $nDuration, self::$s_sPath . $sPath, self::$s_sDomain, $bSecure, false)) {
			$_COOKIE[$sName] = ''.$mValue;
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function load($sName, $mDefault) {
		return isset($_COOKIE[$sName]) ? $_COOKIE[$sName] : $mDefault;
	}
	
	public static function reset($sName) {
		setcookie($sName, '', time()-1);
		unset($_COOKIE[$sName]);
	}
}
