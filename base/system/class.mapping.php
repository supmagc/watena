<?php

class Mapping extends Object {
	
	private $m_aData;
	
	private function __construct(array $aData = array()) {
		$this->m_aData = $aData;
	}
	
	public function Mathes(Filter $oFilter) {
		
	}
	
	public static function LoadFromRequest() {
		return new Mapping(array(
			'usergent' => Request::useragent(),
			'protocol' => Request::protocol(),
			'host' => Request::host(),
			'port' => Request::port(),
			'offset' => Request::offset(),
			'path' => Request::map(),
			'session' => $_SESSION,
			'cookie' => $_COOKIE,
			'post' => $_POST,
			'get' => $_GET
		));
	}
	
	public static function LoadFromUrl(Url $oUrl) {
		return new Mapping(array(
		));
	}
}

?>