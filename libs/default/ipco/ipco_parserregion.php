<?php

class IPCO_ParserRegion extends IPCO_Base {
	
	private $m_sName;
	private $m_aLines = array();
	private $m_bHasContent = false;
	
	public function __construct($sName, IPCO $ipco) {
		parent::__construct($ipco);
		$this->m_sName = $sName;
	}
	
	public function getName() {
		return $this->m_sName;
	}
	
	public function hasContent() {
		return $this->m_bHasContent;
	}
	
	public function build() {
		return '' .
			IPCO_ParserSettings::getFilterRegion($this->m_sName) .
			implode('', $this->m_aLines) .
			IPCO_ParserSettings::getFilterEndRegion();
	}
	
	public function addLine($sLine) {
		if(!$this->m_bHasContent && !Encoding::regMatch(IPCO_ParserSettings::CONTENT_EMPTY_PATERN, $sLine)) {
			$this->m_bHasContent = true;
		}
		$this->m_aLines []= $sLine;
	}
	
	public function addLines(array $aLines) {
		foreach($aLines as $mLine) {
			if(is_array($mLine)) $this->addLines($mLine);
			else $this->addLine($mLine);
		}
	}
}

?>