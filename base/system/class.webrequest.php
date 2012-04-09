<?php

class WebRequest extends Object {

	private $m_sUrl;
	private $m_sMethod;
	private $m_oCurl = null;
	private $m_aOptions = array();
	private $m_aFields = array();
	private $m_aHeaders = array();

	public static $OPTIONS_DEFAULT = array(
	CURLOPT_HEADER 			=> true,
	CURLOPT_RETURNTRANSFER 	=> true,
	CURLOPT_USERAGENT 		=> 'watena-curl',
	CURLOPT_USERPWD 		=> '', // user:pass
	CURLOPT_CONNECTTIMEOUT 	=> 10,
	CURLOPT_TIMEOUT			=> 60
	);

	public function __construct($sUrl, $sMethod) {
		$this->m_sUrl = $sUrl;
		$this->m_sMethod = $sMethod;
		$this->m_oCurl = curl_init($sUrl);
		$this->m_aOptions = self::$OPTIONS_DEFAULT;
		if(stripos($this->m_sUrl, 'https://') === 0)
		$this->setSllCertificate(PATH_DATA . '/ca-certificates/cacert.pem');
	}

	public function __destruct() {
		curl_close($this->m_oCurl);
	}

	public function getCurl() {
		return $this->m_oCurl;
	}

	public function getUrl() {
		return $this->m_sUrl;
	}

	public function getMethod() {
		return $this->m_sMethod;
	}

	public function setSllCertificate($sPath) {
		$this->m_aOptions[CURLOPT_CAINFO] = $sPath;
		$this->m_aOptions[CURLOPT_SSL_VERIFYPEER] = true;
		$this->m_aOptions[CURLOPT_SSL_VERIFYHOST] = 2;
	}

	public function setUseragent($sUseragent) {
		$this->m_aOptions[CURLOPT_USERAGENT] = $sUseragent;
	}

	public function setLogin($sUser, $sPass) {
		$this->m_aOptions[CURLOPT_USERPWD] = urlencode($sUser) . ':' . urlencode($sPass);
	}

	public function addField($sKey, $mValue) {
		$this->m_aFields[$sKey] = $mValue;
	}
	
	public function addFields(array $aFields) {
		$this->m_aFields = array_merge($this->m_aFields, $aFields);
	}

	public function addHeader($sKey, $mValue) {
		$this->m_aHeaders[$sKey] = $mValue;
	}

	public function addHeaders($aHeaders) {
		$this->m_aHeaders = array_merge($this->m_aHeaders, $aHeaders);
	}
	
	public function send() {
		curl_setopt($this->m_oCurl, CURLOPT_HTTPHEADER, array_map(create_function('$a, $b', 'return $a.\': \'.$b;'), array_keys($this->m_aHeaders), array_values($this->m_aHeaders)));
		curl_setopt_array($this->m_oCurl, $this->m_aOptions);

		// Add fields and method !!
		if($this->m_sMethod == 'PUT') {
			curl_setopt($this->m_oCurl, CURLOPT_PUT, true);
			die("PUT NYI"); // TODO: add the appropriazte request stuff
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
			$sUrl = $this->m_sUrl . (stristr($this->m_sUrl, '?') ? '&' : '?') . http_build_query($this->m_aFields, null, '&');
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