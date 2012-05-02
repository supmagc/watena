<?php

class HtmlModel extends Model {

	private $m_sCharset = null;
	private $m_sContentType = null;
	private $m_sTitle = null;
	private $m_sDescription = null;
	private $m_aKeywords = array();
	
	public function getRoot() {
		return $this->getWatena()->getMapping()->getRoot();
	}
	
	public function getHost() {
		return $this->getWatena()->getMapping()->getHost();
	}
	
	public function setTitle($sTitle) {
		$this->m_sTitle = $sTitle;
	}
	
	public function getTitle() {
		return $this->m_sTitle;
	}
	
	public function setContentType($sContentType) {
		$this->m_sContentType = $sContentType;
	}
	
	public function getContentType() {
		return $this->m_sContentType ?: 'text/html';
	}
	
	public function setCharset($sCharset) {
		$this->m_sCharset = $sCharset;
	}
	
	public function getCharset() {
		return $this->m_sCharset ?: Encoding::charset();
	}
	
	public function setDescription($sDescription) {
		$this->m_sDescription = $sDescription;
	}
	
	public function getDescription() {
		return $this->m_sDescription;
	}
	
	public function setKeywords(array $aKeywords) {
		$this->m_aKeywords = $aKeywords;
	}
	
	public function addKeyword($sKeyword) {
		$this->m_aKeywords []= $sKeyword;
	}
	
	public function getKeyWords() {
		return implode(', ', $this->m_aKeywords);
	}
}

?>