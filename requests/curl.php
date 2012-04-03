<?php

class WebRequest {
	
	private $m_sMethod;
	private $m_oCurl = null;
	private $m_aOptions = array();
	
	public static $OPTIONS_DEFAULT = array(
		CURLOPT_HEADER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => 'watena-curl-0.1.1',
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
	
	public function send() {
		curl_setopt_array($this->m_oCurl, $this->m_aOptions);
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
}

$oRequest = new WebRequest('http://flandersisafestival.dev/tester.php?dfg=12', 'GET');

echo '<pre>';
print_r($oRequest->send());
echo '</pre>';

?>