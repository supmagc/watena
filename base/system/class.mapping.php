<?php

class Mapping extends Object {
	
	private $m_aData;
	
	protected function __construct(array $aData = array()) {
		parent::__construct();
		$this->m_aData = $aData;
	}
	
	public function getVariable($mKeys) {
		return array_value($this->m_aData, $mKeys);
	}
	
	public static function LoadFromRequest() {
		return new Mapping(array(
			'useragent' => Request::useragent(),
			'scheme' => Request::scheme(),
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
			'scheme' => $oUrl->getScheme(),
			'host' => $oUrl->getHost(),
			'port' => $oUrl->getPort(),
			'path' => $oUrl->getPath(),
			'get' => $oUrl->getParameters()
		));
	}
}

?>