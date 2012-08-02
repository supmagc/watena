<?php

class HtmlModel extends Model {

	private $m_sCharset = null;
	private $m_sContentType = null;
	private $m_sTitle = null;
	private $m_sDescription = null;
	private $m_aKeywords = null;
	
	public function getRoot() {
		return $this->getWatena()->getMapping()->getRoot();
	}
	
	public function getHost() {
		return $this->getWatena()->getMapping()->getHost();
	}
	
	public function getLocal() {
		return $this->getWatena()->getMapping()->getLocal();
	}
	
	public function setTitle($sTitle) {
		$this->m_sTitle = $sTitle;
	}
	
	public function getTitle() {
		return $this->m_sTitle ?: $this->getConfig('title', $this->getWatena()->getMapping()->getHost());
	}
	
	public function setContentType($sContentType) {
		$this->m_sContentType = $sContentType;
	}
	
	public function getContentType() {
		return $this->m_sContentType ?: $this->getConfig('contenttype', 'text/html');
	}
	
	public function setCharset($sCharset) {
		$this->m_sCharset = $sCharset;
	}
	
	public function getCharset() {
		return $this->m_sCharset ?: $this->getConfig('charset', Encoding::charset());
	}
	
	public function setDescription($sDescription) {
		$this->m_sDescription = $sDescription;
	}
	
	public function getDescription() {
		return $this->m_sDescription ?: $this->getConfig('description', '');
	}
	
	public function setKeywords(array $aKeywords) {
		$this->m_aKeywords = $aKeywords;
	}
	
	public function clearKeywords() {
		$this->m_aKeywords = null;
	}
	
	public function addKeyword($sKeyword) {
		if(!is_array($this->m_aKeywords)) $this->m_aKeywords = array();
		$this->m_aKeywords []= $sKeyword;
	}
	
	public function getKeywords() {
		return is_array($this->m_aKeywords) ? implode(', ', $this->m_aKeywords) : $this->getConfig('keywords', '');
	}
}

?>