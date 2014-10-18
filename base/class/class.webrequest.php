<?php

class WebRequest extends Object {
	
	private $m_oUrl = null;
	private $m_oCurl = null;
	private $m_sMethod = 'GET';
	private $m_sPostDataAppend = null;
	private $m_sUserAgent = 'watena-curl';
	private $m_sSslCertificate = 'D:ca-certificates/cacert.pem';
	private $m_aFields = array();
	private $m_aHeaders = array();
	private $m_aCookies = array();

	public static $OPTIONS_DEFAULT = array(
		CURLOPT_HEADER 			=> true,
		CURLOPT_RETURNTRANSFER 	=> true,
		CURLOPT_FOLLOWLOCATION	=> true,
		CURLOPT_CONNECTTIMEOUT 	=> 10,
		CURLOPT_TIMEOUT			=> 60,
		CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_1
	);
	
	public final function __construct(Url $oUrl, $sMethod = 'GET') {
		$this->m_oUrl = $oUrl;
		$this->m_sMethod = $sMethod;
		
		$this->m_oCurl = curl_init($oUrl->toString());
	}
	
	public final function __destruct() {
		curl_close($this->m_oCurl);
	}

	public final function getCurl() {
		return $this->m_oCurl;
	}

	public final function getUrl() {
		return $this->m_oUrl;
	}
	
	public final function getMethod() {
		return $this->m_sMethod;
	}

	public final function setUseragent($sUserAgent) {
		$this->m_sUserAgent = $sUserAgent;
	}
	
	public final function getUserAgent() {
		return $this->m_sUserAgent;
	}
	
	public final function setPostDataAppend($sPostDataAppend) {
		$this->m_sPostDataAppend = $sPostDataAppend;
	}
	
	public final function getPostDataAppend() {
		return $this->m_sPostDataAppend;
	}

	public final function addField($sKey, $mValue) {
		$this->m_aFields[$sKey] = $mValue;
	}

	public final function addFields(array $aFields) {
		$this->m_aFields = array_merge($this->m_aFields, $aFields);
	}
	
	public final function getFields() {
		return $this->m_aFields;
	}
	
	public final function getFieldsAsString() {
		return http_build_query($this->m_aFields, null, '&');
	} 

	public final function addHeader($sKey, $mValue) {
		$this->m_aHeaders[$sKey] = $mValue;
	}

	public final function addHeaders(array $aHeaders) {
		$this->m_aHeaders = array_merge($this->m_aHeaders, $aHeaders);
	}
	
	public final function getHeaders() {
		return $this->m_aHeaders;
	}
	
	public final function getHeadersAsList() {
		return array_map(create_function('$a, $b', 'return $a.\': \'.$b;'), array_keys($this->m_aHeaders), array_values($this->m_aHeaders));
	}
	
	public final function getHeadersAsString() {
		return implode("\r\n", $this->getHeadersAsList());
	}
	
	public final function addCookie($sKey, $mValue) {
		$this->m_aCookies[$sKey] = $mValue;
	}
	
	public final function addCookies(array $aCookies) {
		$this->m_aCookies = array_merge($this->m_aCookies, $aCookies);
	}
	
	public final function getCookies() {
		return $this->m_aCookies;
	}
	
	public final function getCookiesAsString() {
		return http_build_query($this->m_aCookies, null, '; ');
	}
	
	public final function takeCookies() {
		$this->addCookies($_COOKIE);
	}
	
	public final function getCurlOptions() {
		$aOptions = self::$OPTIONS_DEFAULT;
		
		if($this->m_oUrl->getUserName() && $this->m_oUrl->getPassword()) {
			$aOptions[CURLOPT_USERPWD] = urlencode($this->m_oUrl->getUserName()) . ':' . urlencode($this->m_oUrl->getPassword());
		}
		
		if($this->m_oUrl->getScheme() == 'https') {
			$aOptions[CURLOPT_CAINFO] = $this->getWatena()->getPath($this->m_sSslCertificate);
			$aOptions[CURLOPT_SSL_VERIFYPEER] = true;
			$aOptions[CURLOPT_SSL_VERIFYHOST] = 2;
		}
		
		$aOptions[CURLOPT_USERAGENT] = $this->m_sUserAgent;
		$aOptions[CURLOPT_HTTPHEADER] = $this->getHeadersAsList();
		$aOptions[CURLOPT_COOKIE] = $this->getCookiesAsString();
		
		if($this->m_sMethod == 'POST') {
			$aOptions[CURLOPT_POST] = true;
			$aOptions[CURLOPT_POSTFIELDS] = $this->getFieldsAsString();
			if($this->m_sPostDataAppend) {
				$aOptions[CURLOPT_POSTFIELDS] .= ($aOptions[CURLOPT_POSTFIELDS] ? '&' : '') . $this->m_sPostDataAppend;
			}
		}
		else {
			if($this->m_sMethod == 'PUT')
				$aOptions[CURLOPT_PUT] = true;
			else if($this->m_sMethod == 'GET')
				$aOptions[CURLOPT_HTTPGET] = true;
			else
				$aOptions[CURLOPT_CUSTOMREQUEST] = $this->m_sMethod;
			
			$oUrl = clone $this->m_oUrl;
			$oUrl->addParameters($this->getFields());
			$aOptions[CURLOPT_URL] = $oUrl->toString();
		}
		
		return $aOptions;
	}
	
	public final function send() {
		// Set options
		curl_setopt_array($this->getCurl(), $this->getCurlOptions());

		// Send request
		$mData = curl_exec($this->m_oCurl);
		
		if($mData === false) {
			throw new WebException($this);
		}
		return new WebResponse($this->m_oCurl, $mData);
	}
}
?>