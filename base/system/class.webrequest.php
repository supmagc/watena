<?php

class WebRequest extends Object {

	private $m_sScheme;
	private $m_sUrl;
	private $m_sMethod;
	private $m_oCurl = null;
	private $m_aOptions = array();
	private $m_aFields = array();
	private $m_aHeaders = array();
	private $m_aCookies = array();

	public static $OPTIONS_DEFAULT = array(
		CURLOPT_HEADER 			=> true,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_USERAGENT 		=> 'watena-curl',
		CURLOPT_CONNECTTIMEOUT 	=> 10,
		CURLOPT_TIMEOUT			=> 60,
		CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1
	);
	
	public final function __construct($sUrl, $sMethod = 'GET') {
		$aData = parse_url($sUrl);
		$this->m_sUrl = $aData['scheme'] . '://' . $aData['host'] . (isset($aData['port']) ? ":{$aData[port]}" : '') . $aData['path'];
		$this->m_sScheme = $aData['scheme'];
		$this->m_sMethod = $sMethod;
		$this->m_oCurl = curl_init($this->getUrl());
		$this->m_aOptions = self::$OPTIONS_DEFAULT;
		if(isset($aData['query']))
			parse_str($aData['query'], $this->m_aFields);
		if(isset($aData['user']) && isset($aData['pass']))
			$this->setLogin($aData['user'], $aData['pass']);
		if($this->m_sScheme == 'https')
			$this->setSllCertificate(PATH_DATA . '/ca-certificates/cacert.pem');
	}

	public final function __destruct() {
		curl_close($this->m_oCurl);
	}

	public final function getCurl() {
		return $this->m_oCurl;
	}

	public final function getUrl() {
		return $this->m_sUrl;
	}

	public final function getScheme() {
		return $this->m_sScheme;
	}
	
	public final function getMethod() {
		return $this->m_sMethod;
	}

	public final function setSllCertificate($sPath) {
		$this->m_aOptions[CURLOPT_CAINFO] = file_exists($sPath) ? $sPath : (PATH_DATA . '/' . $sPath);
		$this->m_aOptions[CURLOPT_SSL_VERIFYPEER] = true;
		$this->m_aOptions[CURLOPT_SSL_VERIFYHOST] = 2;
	}

	public final function setUseragent($sUseragent) {
		$this->m_aOptions[CURLOPT_USERAGENT] = $sUseragent;
	}

	public final function setLogin($sUser, $sPass) {
		$this->m_aOptions[CURLOPT_USERPWD] = urlencode($sUser) . ':' . urlencode($sPass);
	}

	public final function addField($sKey, $mValue) {
		$this->m_aFields[$sKey] = $mValue;
	}
	
	public final function addFields(array $aFields) {
		$this->m_aFields = array_merge($this->m_aFields, $aFields);
	}

	public final function addHeader($sKey, $mValue) {
		$this->m_aHeaders[$sKey] = $mValue;
	}

	public final function addHeaders(array $aHeaders) {
		$this->m_aHeaders = array_merge($this->m_aHeaders, $aHeaders);
	}
	
	public final function addCookie($sKey, $mValue) {
		$this->m_aCookies[$sKey] = $mValue;
	}
	
	public final function addCookies($aCookies) {
		$this->m_aCookies = array_merge($this->m_aCookies, $aCookies);
	}
	
	public final function takeCookies() {
		$this->addCookies($_COOKIE);
	}
	
	public final function send() {
		$aHeaders = array_map(create_function('$a, $b', 'return $a.\': \'.$b;'), array_keys($this->m_aHeaders), array_values($this->m_aHeaders));
		$sCookies = http_build_query($this->m_aCookies, null, '; ');
		curl_setopt($this->m_oCurl, CURLOPT_HTTPHEADER, $aHeaders);
		curl_setopt($this->m_oCurl, CURLOPT_COOKIE, $sCookies);
		curl_setopt_array($this->m_oCurl, $this->m_aOptions);

		// Add fields and method !!
		if($this->m_sMethod == 'PUT') {
			curl_setopt($this->m_oCurl, CURLOPT_PUT, true);
			NYI();
		}
		else if($this->m_sMethod == 'POST') {
			curl_setopt($this->m_oCurl, CURLOPT_POST, true);
			curl_setopt($this->m_oCurl, CURLOPT_POSTFIELDS, http_build_query($this->m_aFields, null, '&'));
		}
		else {
			if($this->m_sMethod == 'GET')
				curl_setopt($this->m_oCurl, CURLOPT_HTTPGET, true);
			else
				curl_setopt($this->m_oCurl, CURLOPT_CUSTOMREQUEST, $this->m_sMethod);
			$sUrl = $this->getUrl() . '?' . http_build_query($this->m_aFields, null, '&');
			curl_setopt($this->m_oCurl, CURLOPT_URL, $sUrl);
		}

		$mData = curl_exec($this->m_oCurl);
		if($mData === false) {
			throw new WebException($this);
		}
		return new WebResponse($this->m_oCurl, $mData);
	}
}
?>