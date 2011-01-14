<?php

class TemplateReader {

	private $m_sContent;
	private $m_nIndex;
	private $m_nLength;
	private $m_nMark;
	
	public function __construct($sContent) {
		$this->m_sContent = $sContent;
		$this->m_nIndex = -1;
		$this->m_nLength = strlen($this->m_sContent);
	}
	
	public function read() {
		++$this->m_nIndex;
		return $this->m_nIndex < $this->m_nLength;
	}
	
	public function get() {
		return $this->m_sContent[$this->m_nIndex];
	}
	
	public function setMark($nOffset = 0) {
		$this->m_nMark = $this->m_nIndex + $nOffset + 1;
	}
	
	public function getMark($nOffset = 0) {
		return substr($this->m_sContent, $this->m_nMark, $this->m_nIndex - $this->m_nMark + $nOffset);
	}
	
	public function isStartOff($sIndicator) {
		return substr($this->m_sContent, $this->m_nIndex, strlen($sIndicator)) === $sIndicator;
	}
}

?>