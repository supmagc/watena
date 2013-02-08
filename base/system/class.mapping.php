<?php

class Mapping extends Object {

	private $m_sUserAgent;
	private $m_oUrl;
	
	public function __construct() {
		/*
		$sUrl = Request::protocol() . '://';
		if($_SERVER["SERVER_PORT"] != 80) {
			$sUrl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}
		else {
			$sUrl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$this->m_oUrl = new Url($sUrl);
		*/
	}
	
	public static function init() {
		// Assure REQUEST_URI
		/*
		if(!isset($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
			if(isset($_SERVER['QUERY_STRING'])) {
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
			}
		}
		*/
		// Assure SERVER_PORT
		
		// Assure HTTP_USER_AGENT
	}
}

?>