<?php

class WebRequest {
	
	private $m_sMethod;
	private $m_oCurl = null;
	private $m_aOptions = array();
	private $m_aFields = array();
	
	public static $OPTIONS_DEFAULT = array(
		CURLOPT_HEADER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => 'watena-curl',
		CURLOPT_USERPWD => '', // user:pass
	);

	public function __construct($sUrl, $sMethod) {
		$this->m_sMethod = $sMethod;
		$this->m_oCurl = curl_init($sUrl);
		$this->m_aOptions = self::$OPTIONS_DEFAULT;
	}
	
	public function __destruct() {
		curl_close($this->m_oCurl);
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
	
	public function send() {
		curl_setopt_array($this->m_oCurl, $this->m_aOptions);
		
		// Add fields and method !!
		
		$mData = curl_exec($this->m_oCurl);
		return $mData === false ? false : new WebResponse($this->m_oCurl, $mData);
	}
}

class WebResponse {
	
	private $m_sHeader;
	private $m_sContent;
	private $m_nHeaderSize;
	private $m_nContentSize;
	private $m_nHttpCode;
	private $m_nDuration;
	private $m_nRedirects;
	private $m_sContentType;
	private $m_sCharset;
	private $m_aHeaders = array();
	
	public function __construct($oCurl, $sData) {
		$this->m_nHeaderSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
		$this->m_nHttpCode = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
		$this->m_nDuration = curl_getinfo($oCurl, CURLINFO_TOTAL_TIME);
		$this->m_nRedirects = curl_getinfo($oCurl, CURLINFO_REDIRECT_COUNT);

		$aMatches = array();
		$sContentType = curl_getinfo($oCurl, CURLINFO_CONTENT_TYPE);
		if(preg_match('%([-a-z]+/[-a-z]+)%i', $sContentType, $aMatches)) {
			$this->m_sContentType = $aMatches[1];
		}
		if(preg_match('%;charset=([-a-z0-8]+)%i', $sContentType, $aMatches)) {
			$this->m_sCharset = $aMatches[1];
		}
		
		$this->m_sHeader = substr($sData, 0, $this->m_nHeaderSize);
		$this->m_sContent = substr($sData, strlen($this->m_sHeader));
		$this->m_nContentSize = strlen($this->m_sContent);
		
		$aHeaders = explode("\r\n", $this->m_sHeader);
		foreach($aHeaders as $nIndex => $mValue) {
			$aHeader = explode(':', $mValue, 2);
			if(count($aHeader) > 1) $this->m_aHeaders[$aHeader[0]] = trim($aHeader[1]);
		}
	}
	
	public function getHeader() {
		return $this->m_sHeader;
	}
	
	public function getContent() {
		return $this->m_sContent;
	}
	
	public function getHeaderSize() {
		return $this->m_nHeaderSize;
	}
	
	public function getContentSize() {
		return $this->m_nContentSize();
	}
	
	public function getHttpCode() {
		return $this->m_nHttpCode;
	}
	
	public function getDuration() {
		return $this->m_nDuration;
	}
	
	public function getRedirects() {
		return $this->m_nRedirects;
	}
	
	public function getContentType() {
		return $this->m_sContentType;
	}
	
	public function getCharset() {
		return $this->m_sCharset;
	}
	
	public function getHeader($sKey) {
		return isset($this->m_aHeaders[$sKey]) ? $this->m_aHeaders[$sKey] : false;
	}
}

$oRequest = new WebRequest('http://localhost/watena/tester.php?dfg=12', 'GET');

echo '<pre>';
print_r($oRequest->send());
echo '</pre>';

?>