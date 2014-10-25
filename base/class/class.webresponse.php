<?php
/**
 * Handles the response after a webrequest.
 * On creation this class extracts the needed data from a given url-resource.
 * Most functions are helpers to handle the response data.
 * 
 * Update Notes:
 * 0.1.0
 * - Initial version
 * 
 * @author Jelle Voet
 * @version 0.1.0
 */
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
		$aPositions = array();
		$sContentType = curl_getinfo($oCurl, CURLINFO_CONTENT_TYPE);
		if(Encoding::regFind('([-a-zA-Z0-9]+/[-a-zA-Z0-9]+)', $sContentType, $aMatches, $aPositions)) {
			$this->m_sContentType = $aMatches[1];
		}
		if(Encoding::regFind(';charset=([-a-zA-Z0-9]+)', $sContentType, $aMatches, $aPositions)) {
			$this->m_sCharset = $aMatches[1];
		}

		Encoding::convertByRef($sData);
		$this->m_sHeaders = Encoding::substring($sData, 0, $this->m_nHeadersSize);
		$this->m_sContent = Encoding::substring($sData, strlen($this->m_sHeaders));
		$this->m_nContentSize = Encoding::length($this->m_sContent);

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
		return $this->m_nContentSize;
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
	
	public function getCookies() {
		NYI();
	}
	
	public function makeCookies() {
		NYI();
	}
	
	public function saveToFile($sFilePath) {
		$fp = fopen($sFilePath, 'wb');
		fwrite($fp, $this->getContent());
		fclose($fp);
	}

	public function getHeader($sKey) {
		return isset($this->m_aHeaders[$sKey]) ? $this->m_aHeaders[$sKey] : false;
	}
}
?>