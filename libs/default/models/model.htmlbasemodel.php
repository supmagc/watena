<?php

class HtmlBaseModel extends Model {
	
	private $m_sTitle = '';
	private $m_sDescription = '';
	private $m_aKeywords = array();
	
	public function setTitle($sTitle) {
		$this->m_sTitle = $sTitle;
	}
	
	public function getTitle() {
		return $this->m_sTitle;
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
	
	public function getKeyWords() {
		return implode(', ', $this->m_aKeywords);
	}
}

?>