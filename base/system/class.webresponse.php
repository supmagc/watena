<?php

class WebResponse extends Object {

	private $m_sHeaders;
	private $m_sContent;
	private $m_nHeadersSize;
	private $m_nContentSize;
	private $m_nHttpCode;
	private $m_nDuration;
	private $m_nRedirects;
	private $m_sContentType;
	private $m_sCharset;
	private $m_aHeaders = array();

	public function __construct($oCurl, $sData) {
		$this->m_nHeadersSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
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

		$this->m_sHeaders = substr($sData, 0, $this->m_nHeadersSize);
		$this->m_sContent = substr($sData, strlen($this->m_sHeaders));
		$this->m_nContentSize = strlen($this->m_sContent);

		$aHeaders = explode("\r\n", $this->m_sHeaders);
		foreach($aHeaders as $nIndex => $mValue) {
			$aHeader = explode(':', $mValue, 2);
			if(count($aHeader) > 1) $this->m_aHeaders[$aHeader[0]] = trim($aHeader[1]);
		}
	}

	public function getHeaders() {
		return $this->m_sHeaders;
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
?>