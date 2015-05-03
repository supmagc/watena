<?php
/**
 * Class containing the data for a mapping as used when checking filters.
 * 
 * This can be used for the current request mapping: Mapping::loadFromRequest()
 * This can be used for a custom url mapping: Mapping:loadFromUrl()
 * 
 * The following variables are available in both request and url mappings:
 * - scheme
 * - host
 * - port
 * - path
 * - get
 * 
 * The following variables are only available in a request mapping:
 * - useragent
 * - offset
 * - mapping
 * - session
 * - cookie
 * - post
 * - get
 * 
 * @see Filter
 * @author Jelle
 * @version 0.2.0
 */
class Mapping extends Object {
	
	private $m_aData;
	
	protected function __construct(array $aData = array()) {
		parent::__construct();
		$this->m_aData = $aData;
	}

	/**
	 * Get a named variable.
	 * If a string is given, a variable with the given name will be returned.
	 * If an array is given, the
	 * 
	 * @see array_value()
	 * @param string|string[] $mKeys
	 * @return mixed
	 */
	public function getVariable($mKeys) {
		return array_value($this->m_aData, $mKeys);
	}

	/**
	 * Load a mapping from the current request.
	 * 
	 * @return Mapping
	 */
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
	
	/**
	 * Load amapping from the given url.
	 * 
	 * @param Url $oUrl
	 * @return Mapping
	 */
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
