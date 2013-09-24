<?php

class Mapping extends Object {
	
	private $m_aData;
	
	protected function __construct(array $aData = array()) {
		parent::__construct();
		$this->m_aData = $aData;
	}
	
	public function Mathes(Filter $oFilter) {
		return true;
	}
	
	public static function LoadFromRequest() {
		return new Mapping(array(
			'useragent' => Request::useragent(),
			'protocol' => Request::protocol(),
			'host' => Request::host(),
			'port' => Request::port(),
			'offset' => Request::offset(),
			'path' => Request::path(),
			'mapping' => Request::mapping(),
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