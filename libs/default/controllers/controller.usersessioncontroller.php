<?php
require_plugin('User');

abstract class UserSessionController extends Controller {

	public final function setSession($sKey, $mData) {
		if(!isset($_SESSION['DATA'])) $_SESSION['DATA'] = array();
		$_SESSION['DATA'][$sKey] = $mData;
	}

	public final function getSession($sKey, $mDefault) {
		return hasSession($sKey) ? $_SESSION['DATA'][$sKey] : $mDefault;
	}

	public final function hasSession($sKey) {
		return isset($_SESSION['DATA']) && isset($_SESSION['DATA'][$sKey]);
	}

	public final function getUser() {
		
	}	
}

?>